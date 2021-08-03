/**
 * @copyright: Bizlink
 * @jira: 
 * @author: Ventsislav Verchov vverchov@bizlink-solutions.eu
 */
/**
 * @module ModuleBizlinkCustom
 */
 import { Component, OnInit, EventEmitter,Output } from '@angular/core';
 import { modal } from '../../../services/modal.service';
 
 @Component({
     selector: 'fields-update',
     templateUrl: './src/custom/bizlinkcustom/templates/fieldsforupdate.html'
 })
 
 export class FieldsForUpdate implements OnInit {
 
    @Output() public dataEvent: EventEmitter<any>  = new EventEmitter<any>()
    
    public dataForUpdate = [];
    constructor(
         private modal: modal
    ) {}
 
    ngOnInit(): void {
    }
 
    statusChanged(v){
        var obj = {status: v};
        this.dataForUpdate.push(obj);
    }
 
    save(){
        this.dataEvent.emit(this.dataForUpdate);
        this.closed();
    }
 
    closed(){
        this.modal.closeAllModals();
    }
}