<template>
    <div class="container">
      <div class="sidebar">
        <button @click="highlightContinent('America')">América</button>
        <button @click="highlightContinent('Africa')">África</button>
        <button @click="highlightContinent('Asia')">Ásia</button>
        <button @click="highlightContinent('Europa')">Europa</button>
        <button @click="highlightContinent('Oceania')">Oceania</button>
      </div>
      <div id="map" class="map"></div>
    </div>
  </template>
  
  <script>
  import L from 'leaflet';
  // Importe os arquivos GeoJSON (ajuste os caminhos se necessário)
  import americaGeoJSON from '../../maps/america.json'; 
  import africaGeoJSON from '../../maps/africa.json';
  // ... importe os outros GeoJSONs ...
  
  export default {
    mounted() {
      this.initMap();
    },
    methods: {
      initMap() {
        this.map = L.map('map', {
    dragging: false, // Desabilita o arrasto
    zoomControl: false, // Remove o controle de zoom
    scrollWheelZoom: false, // Desabilita o zoom com a roda do mouse
    doubleClickZoom: false, // Desabilita o zoom com duplo clique
    touchZoom: false, // Desabilita o zoom com toque em dispositivos touch
  }).setView([0, 0], 2);
  
        this.americaLayer = L.geoJSON(americaGeoJSON, {
          style: { fillColor: 'red', weight: 1 }
        }).addTo(this.map);
  
        this.africaLayer = L.geoJSON(africaGeoJSON, {
          style: { fillColor: 'green', weight: 1 }
        }).addTo(this.map);
  
        // ... adicione os outros continentes ...
      },
      highlightContinent(continent) { 
        // Remova o destaque de todos os continentes (ajuste as cores)
        this.americaLayer.setStyle({ fillColor: 'red' }); 
        this.africaLayer.setStyle({ fillColor: 'green' });
        // ... repita para os outros continentes ...
  
        // Destaque o continente selecionado
        if (continent === 'America') {
          this.americaLayer.setStyle({ fillColor: 'yellow' });
        } else if (continent === 'Africa') {
          this.africaLayer.setStyle({ fillColor: 'yellow' });
        }
        // ... repita para os outros continentes ...
      }
    }
  };
  </script>
<style>
.container {
  display: flex;
}

.sidebar {
  width: 200px;
}

.map {
  flex: 1;
  height: 50    0px; 
}
</style>