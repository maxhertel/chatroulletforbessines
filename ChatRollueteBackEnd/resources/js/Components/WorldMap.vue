<template>

  <div class="flex flex-row ">
    <div class="basis-1/4">
      <div class="flex flex-col-reverse ">
        <div> 
          <button
           @click="highlightContinent('Africa')"
            class="mt-3 w-full rounded-md bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
            type="button">
            África
          </button>
        </div>
        <div> 
          <button @click="highlightContinent('America')"
            class="mt-3 w-full rounded-md bg-slate-800 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
            type="button">
            America
          </button>
        </div>
        <div> 
          <button  @click="highlightContinent('Asia')"class=" mt-3 w-full rounded-md bg-slate-800 py-2 px-4 border border-slate-800 text-center text-sm text-white 
         shadow-md hover:shadow-lg focus:bg-slate-700 focus:border-slate-700 focus:shadow-none 
         active:bg-slate-700 active:border-slate-700 active:shadow-none 
          ml-2" type="button">
          Ásia
          </button>
        </div>
        <div> 
          <button  @click="highlightContinent('Europe')"class="mt-3 w-full rounded-md bg-slate-800 py-2 px-4 border border-slate-800 text-center text-sm text-white 
         shadow-md hover:shadow-lg focus:bg-slate-700 focus:border-slate-700 focus:shadow-none 
         active:bg-slate-700 active:border-slate-700 active:shadow-none 
          ml-2" type="button">
          Europa
          </button>
        </div>
        <div> 
          <button  @click="highlightContinent('Oceania')"class="mt-3 w-full rounded-md bg-slate-800 py-2 px-4 border border-slate-800 text-center text-sm text-white 
         shadow-md hover:shadow-lg focus:bg-slate-700 focus:border-slate-700 focus:shadow-none 
         active:bg-slate-700 active:border-slate-700 active:shadow-none 
          ml-2" type="button">
          Oceania
          </button>
        </div>
      </div>
    </div>
    <div class="basis-3/4 " >
      <div id="map" style="min-height: 600px;" ></div>
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

      // Define o tamanho máximo do mapa
      this.map.setMaxBounds([
    [-90, -180], // Canto inferior esquerdo
    [90, 180]   // Canto superior direito
  ]);

      this.americaLayer = L.geoJSON(americaGeoJSON, {
        style: {   fillColor: 'green', weight: 0, fillOpacity: 1  }
      }).addTo(this.map);

      this.africaLayer = L.geoJSON(africaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1}
      }).addTo(this.map);

      this.asiaLayer = L.geoJSON(asiaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1}
      }).addTo(this.map);

      this.europeLayer = L.geoJSON(europeGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1}
      }).addTo(this.map);

      this.oceaniaLayer = L.geoJSON(oceaniaGeoJSON, {
        style: { fillColor: 'green', weight: 0, fillOpacity: 1}
      }).addTo(this.map);

       // Ajusta o zoom para que todos os dados caibam no mapa
  // this.map.fitBounds(
  //   L.geoJSON(americaGeoJSON).getBounds()
  //     .extend(L.geoJSON(africaGeoJSON).getBounds())
  //     .extend(L.geoJSON(asiaGeoJSON).getBounds())
  //     .extend(L.geoJSON(europeGeoJSON).getBounds())
  //     .extend(L.geoJSON(oceaniaGeoJSON).getBounds())
  //     // ... estenda para outros continentes
  // );
    },
    highlightContinent(continent) {
      // Remova o destaque de todos os continentes (ajuste as cores)
      this.americaLayer.setStyle({ fillColor: 'green' });
      this.africaLayer.setStyle({ fillColor: 'green' });
      this.asiaLayer.setStyle({ fillColor: 'green' });
      this.europeLayer.setStyle({ fillColor: 'green' });
      this.oceaniaLayer.setStyle({ fillColor: 'green' });
      // ... repita para os outros continentes ...

      // Destaque o continente selecionado
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
      // ... repita para os outros continentes ...
    }
  }
};
</script>