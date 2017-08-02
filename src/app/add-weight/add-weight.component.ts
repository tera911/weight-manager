import {Component, Injectable, OnInit} from '@angular/core';
import {Http} from "@angular/http";

@Component({
  selector: 'app-add-weight',
  templateUrl: './add-weight.component.html',
  styleUrls: ['./add-weight.component.css']
})

@Injectable()
export class AddWeightComponent implements OnInit {
  public test: any;
  public weight: Number;

  constructor(private http: Http) { }

  ngOnInit() {
  }

    public save(): void {
      this.http.post('/api/weight', {weight: this.weight}).toPromise();
    }
}
