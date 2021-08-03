/**
 * @copyright: Bizlink
 * @jira: 
 * @author: Ventsislav Verchov vverchov@bizlink-solutions.eu
 */
/**
 * @module ModuleBizlinkCustom
 */
 import { Component, OnInit } from '@angular/core';
 import { modal } from '../../../services/modal.service';
 import { backend } from '../../../services/backend.service';
 import { modellist } from '../../../services/modellist.service';
 
 @Component({
     selector: 'mass-deleted',
     template: '<span>Редактирайте избраните</span>'
 })
 
 export class MassUpdate implements OnInit {
    private updateItems:any;
     constructor(
         private modal: modal,
         private backend: backend,
         private modellist: modellist
     ) {}
 
     ngOnInit(): void {
     }
     private sendMassUpdateRequest(data){
        if(data.length>0){
            this.modal.confirm("Сигурни ли сте, че искате да редактирате записа/ите!","Внимание!").subscribe(res=>{
                if (res) {
                    let stopper = this.modal.await("Моля изчакайте");
                    this.backend.postRequest(`/massupdate/`, {},{
                        body: {
                            model: this.modellist.module,
                            updateItams: this.updateItems,
                            fields: data
                        }
                    }).subscribe(res1 => {
                        console.log("Update Items");
                        stopper.emit(true);
                        this.modellist.getListData();
                    }, err => {
                        stopper.emit(true);
                        this.modal.info(`Възникна грешка! Опитайте отново.`);
                        this.modellist.getListData();
                    });
                }
            })
        }
     }
     public execute() {
        if(this.modellist.getSelectedIDs().length <=0){
            this.modal.info("Моля, маркирате обект за редакция!","Възникна грешка.","error")
            return;
        }
        this.updateItems = this.modellist.getSelectedIDs();
 
        this.modal.openModal("FieldsForUpdate").subscribe((result => {
            result.instance.dataEvent.subscribe(this.sendMassUpdateRequest.bind(this))
        }).bind(this));
    }
}