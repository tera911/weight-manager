import {Component, Injectable, OnInit, ViewChild} from '@angular/core';
import {Http} from '@angular/http';


import {Observable} from 'rxjs/Observable';
import 'rxjs/add/observable/of';
import 'rxjs/add/observable/throw';
import 'rxjs/add/operator/toPromise';
import {BaseChartDirective} from "ng2-charts";

@Component({
    selector: 'app-dashboard',
    templateUrl: './dashboard.component.html',
    styleUrls: ['./dashboard.component.css']
})

@Injectable()
export class DashboardComponent implements OnInit {

    public user: any = {};
    public isNotLogin = false;

    @ViewChild(BaseChartDirective)
    public chart: BaseChartDirective;

    constructor(private http: Http) {}

    public ngOnInit() {
        this.http.get('http://w.tera.jp/api/user', { withCredentials: true }).subscribe(
        response => {
                this.user = response.json();
            },
            error => {
                if (error.status === 401) {
                    this.isNotLogin = true;
                }
            });

        this.fetchDashBoard().then(res => {
            this.chart.datasets = res.d;
            this.chart.labels = res.date;
            this.chart.ngOnChanges({});
        });
    }

    private async  fetchDashBoard(): Promise<any>  {
        const res = await this.http.get('Http://w.tera.jp/api/dashboard', { withCredentials: true }).toPromise();
        return res.json();
    }

    public lineChartOptions: any = {
        responsive: true
    };
    public lineChartColors: Array<any> = [
        { // grey
            backgroundColor: 'rgba(148,159,177,0.2)',
            borderColor: 'rgba(148,159,177,1)',
            pointBackgroundColor: 'rgba(148,159,177,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(148,159,177,0.8)'
        },
        { // dark grey
            backgroundColor: 'rgba(77,83,96,0.2)',
            borderColor: 'rgba(77,83,96,1)',
            pointBackgroundColor: 'rgba(77,83,96,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(77,83,96,1)'
        },
        { // grey
            backgroundColor: 'rgba(148,159,177,0.2)',
            borderColor: 'rgba(148,159,177,1)',
            pointBackgroundColor: 'rgba(148,159,177,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(148,159,177,0.8)'
        }
    ];
    public lineChartLegend: boolean = true;
    public lineChartType: string = 'line';

    public randomize(): void {
        let _lineChartData: Array<any> = new Array(this.chart.datasets.length);
        for (let i = 0; i < this.chart.datasets.length; i++) {
            _lineChartData[i] = {
                data: new Array(this.chart.datasets[i].data.length),
                label: this.chart.datasets[i].label
            };
            for (let j = 0; j < this.chart.datasets[i].data.length; j++) {
                _lineChartData[i].data[j] = Math.floor((Math.random() * 100) + 1);
            }
        }
        this.chart.datasets = _lineChartData;
        this.chart.ngOnChanges({});
    }

    // events
    public chartClicked(e: any): void {
        console.log(e);
    }

    public chartHovered(e: any): void {
        console.log(e);
    }

}
