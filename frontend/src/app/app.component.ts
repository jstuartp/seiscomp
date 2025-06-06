import { Component, AfterViewInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import * as L from 'leaflet';

interface Earthquake {
  publicID: string;
  hora: string;
  magnitud: number;
  latitud: number;
  longitud: number;
  profundidad: number;
}

@Component({
  selector: 'app-root',
  standalone: true,
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements AfterViewInit {
  earthquakes: Earthquake[] = [];
  map!: L.Map;

  constructor(private http: HttpClient) {}

  ngAfterViewInit() {
    this.map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(this.map);
    this.loadData();
  }

  loadData() {
    this.http.get<Earthquake[]>('/api/earthquakes').subscribe(data => {
      this.earthquakes = data;
      this.drawMarkers();
    });
  }

  drawMarkers() {
    for (const q of this.earthquakes) {
      L.circle([q.latitud, q.longitud], {
        radius: q.magnitud * 2000,
        color: 'red',
        fillOpacity: 0.5
      }).addTo(this.map).bindPopup(`Mag: ${q.magnitud} - ${q.hora}`);
    }
    if (this.earthquakes.length) {
      const bounds = this.earthquakes.map(q => [q.latitud, q.longitud]) as [number, number][];
      this.map.fitBounds(bounds);
    }
  }
}
