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
    public weight: Number;
    public is_add_show: boolean;
    public plainData: any = [];

    @ViewChild(BaseChartDirective)
    public chart: BaseChartDirective;

    constructor(private http: Http) {}

    public ngOnInit() {
        this.http.get('/api/user').subscribe(
        response => {
                this.user = response.json();
            },
            error => {
                if (error.status === 401) {
                    // this.router.navigateByUrl('/login');
                    this.isNotLogin = true;
                }
            });

        this.reloadDashboard();
    }

    private reloadDashboard(): void {
        this.fetchDashBoard().then(res => {
            this.chart.datasets = res.d;
            this.chart.labels = res.date;
            this.chart.ngOnChanges({});
            let keys: Array<Object> = [];
            for (const key in res.plain[0].data) {
                keys.push({
                    key: key,
                    v: res.plain[0].data[key]
                });
            }
            this.plainData = keys;
        });
    }

    private async  fetchDashBoard(): Promise<any>  {
        const res = await this.http.get('/api/dashboard', { withCredentials: true }).toPromise();
        return res.json();
    }

    public lineChartOptions: any = {
        responsive: true
    };
    public lineChartColors: Array<any> = [
        { // grey
            backgroundColor: 'rgba(255,102,123,0.2)',
            borderColor: 'rgba(255,102,123,1)',
            pointBackgroundColor: 'rgba(255,100,123,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(148,159,177,0.8)'
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

    public save(): void {
        let p = this.http.post('/api/weight', {weight: this.weight}).toPromise();
        this.weight = null;
        this.is_add_show = false;
        p.then(() => {
            this.reloadDashboard();
        });
    }
}
