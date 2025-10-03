<template>
  <div class="flex flex-row">
    <div class="basis-1/4">
      <div class="flex flex-col-reverse">
        <div> 
          <button
            @click="highlightContinent('Africa')"
            class="mt-5 w-full  bg-green-800 py-5 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
            type="button">
            África
          </button>
        </div>
        <div> 
          <button 
            @click="highlightContinent('America')"
            class="mt-3 w-full  bg-green-800 py-5 px-4  text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
            type="button">
            America
          </button>
        </div>
        <div> 
          <button 
            @click="highlightContinent('Asia')" 
            class="mt-3 w-full  bg-green-800 py-5 px-4   text-center text-sm text-white 
            shadow-md hover:shadow-lg focus:bg-green-700  focus:shadow-none 
            active:bg-green-700  active:shadow-none 
            ml-2" 
            type="button">
            Ásia
          </button>
        </div>
        <div> 
          <button 
            @click="highlightContinent('Europe')" 
            class="mt-3 w-full  bg-green-800 py-5 px-4   text-center text-sm text-white 
            shadow-md hover:shadow-lg focus:bg-green-700  focus:shadow-none 
            active:bg-green-700  active:shadow-none 
            ml-2" 
            type="button">
            Europa
          </button>
        </div>
        <div> 
          <button 
            @click="highlightContinent('Oceania')" 
            class="mt-3 w-full  bg-green-800 py-5 px-4   text-center text-sm text-white 
            shadow-md hover:shadow-lg focus:bg-green-700  focus:shadow-none 
            active:bg-green-700  active:shadow-none 
            ml-2" 
            type="button">
            Oceania
          </button>
        </div>
      </div>
    </div>

    <div class="basis-3/4">
      <div id="map" style="min-height: 600px;max-height: 600px;"></div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet';
import americaGeoJSON from '../../maps/america.json';
import africaGeoJSON from '../../maps/africa.json';
import asiaGeoJSON from '../../maps/asia.json';
import europeGeoJSON from '../../maps/europe.geo.json';
import oceaniaGeoJSON from '../../maps/oceania.geo.json';

export default {
  data() {
    return {
      selectedContinent: null,
      username: '',
      inChat: false,
      messages: [],
      newMessage: '',
      partnerName: '',
      privateChannel: null,
      map: null,
      americaLayer: null,
      africaLayer: null,
      asiaLayer: null,
      europeLayer: null,
      oceaniaLayer: null
    };
  },
  mounted() {
    this.initMap();
    this.initializeEcho();
  },
  methods: {
    initMap() {
      this.map = L.map('map', {
        dragging: false,
        zoomControl: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        touchZoom: false,
      }).setView([0, 0], 1);

      this.map.setMaxBounds([
        [0, 0],
        [1, 0]
      ]);

      this.americaLayer = L.geoJSON(americaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1 }
      }).addTo(this.map);

      this.africaLayer = L.geoJSON(africaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1 }
      }).addTo(this.map);

      this.asiaLayer = L.geoJSON(asiaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1 }
      }).addTo(this.map);

      this.europeLayer = L.geoJSON(europeGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1 }
      }).addTo(this.map);

      this.oceaniaLayer = L.geoJSON(oceaniaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1 }
      }).addTo(this.map);

      this.map.fitBounds(
        L.geoJSON(americaGeoJSON).getBounds()
          .extend(L.geoJSON(africaGeoJSON).getBounds())
          .extend(L.geoJSON(asiaGeoJSON).getBounds())
          .extend(L.geoJSON(europeGeoJSON).getBounds())
          .extend(L.geoJSON(oceaniaGeoJSON).getBounds())
      );
    },
    highlightContinent(continent) {
      this.selectedContinent = continent;
      
      this.americaLayer.setStyle({ fillColor: 'green' });
      this.africaLayer.setStyle({ fillColor: 'green' });
      this.asiaLayer.setStyle({ fillColor: 'green' });
      this.europeLayer.setStyle({ fillColor: 'green' });
      this.oceaniaLayer.setStyle({ fillColor: 'green' });

      if (continent === 'America') {
        this.americaLayer.setStyle({ fillColor: 'white' });
      } else if (continent === 'Africa') {
        this.africaLayer.setStyle({ fillColor: 'white' });
      } else if (continent === 'Asia') {
        this.asiaLayer.setStyle({ fillColor: 'white' });
      } else if (continent === 'Europe') {
        this.europeLayer.setStyle({ fillColor: 'white' });
      } else if (continent === 'Oceania') {
        this.oceaniaLayer.setStyle({ fillColor: 'white' });
      }
    },
    initializeEcho() {
      window.Echo = new Echo({
        broadcaster: 'reverb',
        key: process.env.MIX_REVERB_APP_KEY,
        wsHost: window.location.hostname,
        wsPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
      });
    },
    goToChat() {
      if (!this.username.trim() || !this.selectedContinent) return;
      
      this.inChat = true;
      this.messages = [];
      
      // Entrar na fila de espera
      axios.post('/api/join-chat', {
        continent: this.selectedContinent,
        username: this.username
      }).then(response => {
        if (response.data.channel) {
          this.privateChannel = response.data.channel;
          
          // Ouvir mensagens no canal privado
          window.Echo.private(this.privateChannel)
            .listen('ChatMessageEvent', (data) => {
              this.messages.push({
                text: data.message,
                sender: data.sender === this.username ? 'me' : 'partner'
              });
              this.partnerName = data.sender === this.username ? '' : data.sender;
            })
            .listen('ChatPartnerFound', (data) => {
              this.partnerName = data.partner;
              this.messages.push({
                text: `Você foi conectado com ${data.partner}!`,
                sender: 'system'
              });
            })
            .listen('ChatEnded', () => {
              this.messages.push({
                text: 'Seu parceiro saiu do chat. Você será reconectado...',
                sender: 'system'
              });
              this.reconnectChat();
            });
        }
      });
    },
    sendMessage() {
      if (!this.newMessage.trim() || !this.privateChannel) return;
      
      axios.post('/api/send-message', {
        channel: this.privateChannel,
        message: this.newMessage,
        sender: this.username
      });
      
      this.messages.push({
        text: this.newMessage,
        sender: 'me'
      });
      
      this.newMessage = '';
    },
    leaveChat() {
      if (this.privateChannel) {
        axios.post('/api/leave-chat', {
          channel: this.privateChannel,
          username: this.username
        });
      }
      
      this.inChat = false;
      this.privateChannel = null;
      this.partnerName = '';
    },
    reconnectChat() {
      this.leaveChat();
      setTimeout(() => this.joinChat(), 2000);
    }
  },
  beforeUnmount() {
    this.leaveChat();
  }
};
</script>

