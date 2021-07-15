/**
 * @copyright: 
 * @jira: T-171
 * @author: Ventsislav Verchov 
 */
/**
 * @module ModuleBizlinkCustom
 */
 import { Component, OnInit } from '@angular/core';
 import { backend } from '../../../services/backend.service';
 import { modellist } from '../../../services/modellist.service';
 
 @Component({
     selector: 'distributeleads',
     template: '<span>Разпредели</span>'
 })
 
 export class DistributeLeads implements OnInit {
 
     constructor(
        private backend: backend,
        private modellist: modellist,
 
     ) {
     }

     ngOnInit(): void {
     }
 
     public execute() {
        let input =  this.modellist.getSelectedIDs();
        this.backend.postRequest(`/distributeleads/`, {}, {
            body: {
                data: input,
            }
 
        }).subscribe(e=>{
            this.modellist.getListData();
        })
 
    }; 
 
 }