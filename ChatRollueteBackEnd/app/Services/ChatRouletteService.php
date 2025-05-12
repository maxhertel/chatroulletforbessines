<?php

namespace App\Services;

use App\Events\ChatRouletteEvent;
use Illuminate\Support\Facades\Redis;

class ChatRouletteService
{
    public function pairUsers($continent, $userId)
    {
        // Armazena usuário esperando por conexão
        Redis::sadd("chat:waiting:$continent", $userId);
        
        // Verifica se há outro usuário esperando
        $waitingUsers = Redis::smembers("chat:waiting:$continent");
        
        if (count($waitingUsers) >= 2) {
            // Remove dois usuários da lista de espera
            $user1 = array_pop($waitingUsers);
            $user2 = array_pop($waitingUsers);
            
            Redis::srem("chat:waiting:$continent", $user1, $user2);
            
            // Cria um canal privado para esses dois usuários
            $privateChannel = "chat:pair:" . md5("$user1-$user2");
            
            // Notifica ambos os usuários sobre o emparelhamento
            event(new ChatRouletteEvent('Você foi conectado!', $privateChannel, 'system'));
            
            return $privateChannel;
        }
        
        return null;
    }
}