<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Events\ChatMessageEvent;
use App\Events\ChatPartnerFound;
use App\Events\ChatEnded;

class ChatController extends Controller
{
    // Tempo máximo de espera por um parceiro (em segundos)
    const MAX_WAIT_TIME = 30;
    
    // Tempo mínimo de conversa (em segundos)
    const MIN_CHAT_TIME = 10;

    /**
     * Entrar na fila de chat para um continente
     */
    public function joinChat(Request $request)
    {
        $request->validate([
            'continent' => 'required|in:Africa,America,Asia,Europe,Oceania',
            'username' => 'required|string|max:30'
        ]);

        $continent = $request->input('continent');
        $username = $request->input('username');
        $userId = uniqid(); // ID único para a sessão

        // Verificar se já está em um chat
        if ($this->isUserInChat($username, $continent)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Você já está em um chat neste continente'
            ], 400);
        }

        // Procurar por um parceiro disponível
        $partner = $this->findChatPartner($continent, $username);

        if ($partner) {
            // Criar canal privado para o par
            $channelName = $this->createPrivateChannel($continent, $username, $partner);
            
            return response()->json([
                'status' => 'success',
                'channel' => $channelName,
                'partner' => $partner
            ]);
        }

        // Se não encontrou parceiro, colocar na fila de espera
        $this->addToWaitingQueue($continent, $username, $userId);

        return response()->json([
            'status' => 'waiting',
            'message' => 'Procurando por um parceiro...'
        ]);
    }

    /**
     * Enviar uma mensagem no chat
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'message' => 'required|string|max:500',
            'sender' => 'required|string|max:30'
        ]);

        $channel = $request->input('channel');
        $message = $request->input('message');
        $sender = $request->input('sender');

        // Verificar se o canal ainda está ativo
        if (!$this->isChannelActive($channel)) {
            return response()->json([
                'status' => 'error',
                'message' => 'O chat foi encerrado'
            ], 400);
        }

        // Disparar evento de mensagem
        event(new ChatMessageEvent($channel, $message, $sender));

        return response()->json([
            'status' => 'success',
            'message' => 'Mensagem enviada'
        ]);
    }

    /**
     * Sair do chat
     */
    public function leaveChat(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'username' => 'required|string|max:30'
        ]);

        $channel = $request->input('channel');
        $username = $request->input('username');

        // Notificar o parceiro que o chat acabou
        event(new ChatEnded($channel));

        // Remover do sistema de chat
        $this->removeUserFromChat($channel, $username);

        return response()->json([
            'status' => 'success',
            'message' => 'Você saiu do chat'
        ]);
    }

    /**
     * Métodos auxiliares privados
     */

    private function findChatPartner($continent, $username)
    {
        // Procurar na fila de espera do continente
        $waitingUsers = Redis::lrange("waiting:$continent", 0, -1);
        
        foreach ($waitingUsers as $userData) {
            $data = json_decode($userData, true);
            
            // Não emparelhar consigo mesmo
            if ($data['username'] !== $username) {
                // Remover da fila de espera
                Redis::lrem("waiting:$continent", 0, $userData);
                return $data['username'];
            }
        }
        
        return null;
    }

    private function addToWaitingQueue($continent, $username, $userId)
    {
        $userData = json_encode([
            'username' => $username,
            'userId' => $userId,
            'joined_at' => now()->toDateTimeString()
        ]);

        // Adicionar no início da fila (LIFO)
        Redis::lpush("waiting:$continent", $userData);
        
        // Configurar timeout
        Redis::expire("waiting:$continent", self::MAX_WAIT_TIME);
    }

    private function createPrivateChannel($continent, $user1, $user2)
    {
        $channelName = "private-chat.$continent." . md5("$user1-$user2-" . now()->timestamp);
        
        // Registrar o canal ativo
        Redis::hset("active_chats", $channelName, json_encode([
            'user1' => $user1,
            'user2' => $user2,
            'continent' => $continent,
            'started_at' => now()->toDateTimeString()
        ]));
        
        // Configurar tempo mínimo de chat
        Redis::expire("active_chats:$channelName", self::MIN_CHAT_TIME);
        
        // Notificar ambos os usuários
        event(new ChatPartnerFound($channelName, $user1, $user2));
        event(new ChatPartnerFound($channelName, $user2, $user1));
        
        return $channelName;
    }

    private function isChannelActive($channel)
    {
        return Redis::hexists("active_chats", $channel);
    }

    private function removeUserFromChat($channel, $username)
    {
        // Remover o chat do registro
        Redis::hdel("active_chats", $channel);
        
        // Remover da fila de espera (caso ainda esteja lá)
        $this->removeFromWaitingQueues($username);
    }

    private function removeFromWaitingQueues($username)
    {
        $continents = ['Africa', 'America', 'Asia', 'Europe', 'Oceania'];
        
        foreach ($continents as $continent) {
            $waitingUsers = Redis::lrange("waiting:$continent", 0, -1);
            
            foreach ($waitingUsers as $key => $userData) {
                $data = json_decode($userData, true);
                
                if ($data['username'] === $username) {
                    Redis::lrem("waiting:$continent", 0, $userData);
                    break;
                }
            }
        }
    }

    private function isUserInChat($username, $continent)
    {
        // Verificar em chats ativos
        $activeChats = Redis::hgetall("active_chats");
        
        foreach ($activeChats as $chatData) {
            $data = json_decode($chatData, true);
            
            if ($data['continent'] === $continent && 
                ($data['user1'] === $username || $data['user2'] === $username)) {
                return true;
            }
        }
        
        // Verificar na fila de espera
        $waitingUsers = Redis::lrange("waiting:$continent", 0, -1);
        
        foreach ($waitingUsers as $userData) {
            $data = json_decode($userData, true);
            
            if ($data['username'] === $username) {
                return true;
            }
        }
        
        return false;
    }
}