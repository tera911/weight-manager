import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpModule, RequestOptions } from '@angular/http';
import { enableProdMode } from '@angular/core';

import { AppComponent } from './app.component';
import { ChartsModule } from 'ng2-charts';
import { DashboardComponent } from './dashboard/dashboard.component';
import { AddWeightComponent } from './add-weight/add-weight.component';
import {FormsModule} from '@angular/forms';

import {CustomRequestOptions} from "./custom-request-options";

enableProdMode();

@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    AddWeightComponent
  ],
  imports: [
    BrowserModule,
    ChartsModule,
    HttpModule,
    FormsModule
  ],
  providers: [
      {provide: RequestOptions, useClass: CustomRequestOptions}
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
