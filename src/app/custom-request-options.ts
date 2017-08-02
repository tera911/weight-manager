import {BaseRequestOptions, Headers} from "@angular/http";

export class CustomRequestOptions  extends BaseRequestOptions{

    constructor(){
        super();
        this.withCredentials = true;
    }
}
