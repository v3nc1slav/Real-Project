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
     template: '<span>Изтрии избраните</span>'
 })
 
 export class MassDeleted implements OnInit {
 
     constructor(
         private modal: modal,
         private backend: backend,
         private modellist: modellist
     ) {}
 
     ngOnInit(): void {
     }
 
     public execute() {
        if(this.modellist.getSelectedIDs().length <=0){
            this.modal.info("Моля, маркирате обект за изтриване!","Възникна грешка.","error")
            return;
        }
        let deletedItams = this.modellist.getSelectedIDs();
 
        this.modal.confirm("Сигурни ли сте, че искате да изтриете записа/ите!","Внимание!").subscribe(res=>{
            if (res) {
                let stopper = this.modal.await("Моля изчакайте");
                this.backend.postRequest(`/massdeleted/`, {},{
                    body: {
                        model: this.modellist.module,
                        deletedItams: deletedItams,
                    }
                }).subscribe(res1 => {
                    console.log("Deleted Items");
                    stopper.emit(true);
                    this.modellist.getListData();
                }, err => {
                    stopper.emit(true);
                    this.modal.info(`Възникна грешка! Опитайте отново.`);
                    this.modellist.getListData();
                });
            }
        });
    }
}
 