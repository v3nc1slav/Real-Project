({
    render: function() {
        this.meta = {};
        this.meta.buttons = [{
            name: 'sidebar_toggle',
            type: 'sidebartoggle'
        }];
 
        this._super("render", [{ accName: this.accountName }]);
        this.renderCustomFields();
    },
    renderCustomFields: function() {
        var account = {
            meta: {
                view: "edit"
            },
            def: {
                type: 'relate',
                name: 'account_filter',
                module: 'Accounts',
                rname: 'name',
                id_name: 'account_filter_id',
                isMultiSelect: false,
            },
            model: null,
            viewName: "edit",
            view: this
        };
        this.assignedUserFilter = app.view.createField(account);
        this.$el.find('#accountField').html(this.assignedUserFilter.el);
        this.assignedUserFilter.render();
 
        //assigned user
        var obj = {
            meta: {
                view: "edit"
            },
            def: {
                type: 'relate',
                name: 'assigned_user_filter',
                module: 'Users',
                rname: 'full_name',
                id_name: 'assigned_user_filter_id',
                isMultiSelect: false,
            },
            model: null,
            viewName: "edit",
            view: this
        };
        this.assignedUserFilter = app.view.createField(obj);
        this.$el.find('#assignedUser').html(this.assignedUserFilter.el);
        this.assignedUserFilter.render();
        //transfer
        var transferObj = {
            meta: {
                view: "edit"
            },
            def: {
                type: 'relate',
                name: 'transfer_assigned_user_filter',
                module: 'Users',
                rname: 'full_name',
                id_name: 'transfer_assigned_user_filter_id',
                isMultiSelect: false,
            },
            model: null,
            viewName: "edit",
            view: this
        };
        this.transferedAssignedUserFilter = app.view.createField(transferObj);
        this.$el.find('#transferAssignedUser').html(this.transferedAssignedUserFilter.el);
        this.transferedAssignedUserFilter.render();
    },
    campaignName: "",
    initialize: function(options) {
        this._super('initialize', [options]);
    },
    openDrawerWallet: function(event) {
 
        var _model, success;
        var data = $(event.currentTarget).data();
        _model = app.data.createBean(data.module, { id: data.id });
        success = _.bind(function(model) {
            model.module = data.module;
            app.drawer.open({
                layout: data.layout,
                context: {
                    id: data.id,
                    model: _model,
                    module: data.module,
                    makeReadOnlyFromDrawer: true
                }
            }, _.bind(function(context, var1) {
                debugger;
                    getWallet(data.id, function(resutl) {
                        editTableRow(resutl);
                    })
            }, this));
        }, this);
        _model.fetch({
            showAlerts: true,
            success: success,
            params: {
                erased_fields: true
            }
        });
    },
    openDrawerCall: function(event) {
        var _model
        var data = $(event.currentTarget).data();
        _model = app.data.createBean(data.module);
        var campaignBean = app.data.createBean('Campaigns', { id: data.campid });
        campaignBean.fetch()
            //alert("hi")
            var fullName = "";
            if(data.contactname == null){
                fullName = data.contactlname;
            }
            else if(data.contactlname == null){
                fullName = data.contactname;
            }else{
                fullName = data.contactname + " " + data.contactlname;
            }
 
        _model.set("parent_type", 'Accounts');
        _model.set("parent_id", data.contactid);
        _model.set("parent_name", data.accname);
        debugger
        _model.set("campaigns_calls_1calls_name", data.campname);
        _model.set('campaigns_calls_1calls_idb', data.campid)
        app.drawer.open({
            layout: data.layout,
            context: {
                create: true,
                model: _model,
                module: data.module
            }
        }, _.bind(function(context, var1) {
                getWallet(data.walletid, function(resutl) {
                    editTableRow(resutl);
                })
        }, this));
    },
    openDrawerMeeting: function(event) {
        var _model
        var data = $(event.currentTarget).data();
        _model = app.data.createBean(data.module);
        var campaignBean = app.data.createBean('Campaigns', { id: data.campid });
        campaignBean.fetch()
            //alert("hi")
            var fullName = "";
            if(data.contactname == null){
                fullName = data.contactlname;
            }
            else if(data.contactlname == null){
                fullName = data.contactname;
            }else{
                fullName = data.contactname + " " + data.contactlname;
            }
 
        _model.set("parent_type", 'Accounts');
        _model.set("parent_id", data.contactid);
        _model.set("parent_name", data.accname);
        debugger
        _model.set("campaigns_meetings_1meetings_name", data.campname);
        _model.set('campaigns_meetings_1meetings_idb', data.campid)
        app.drawer.open({
            layout: data.layout,
            context: {
                create: true,
                model: _model,
                module: data.module
            }
        }, _.bind(function(context, var1) {
                getWallet(data.walletid, function(resutl) {
                    editTableRow(resutl);
                })
        }, this));
    },
    events: {
        'click [data-action=previewRow]': 'previewRow',
        'click [data-action=openDrawerWallet]': 'openDrawerWallet',
        'click [data-action=openDrawerCall]': 'openDrawerCall',
        'click [data-action=openDrawerMeeting]': 'openDrawerMeeting',
        'click [data-action=removeContact]': 'removeContact',
    },
    acceptRemove: true,
    removeContact: function(event) {
            if (this.acceptRemove) {
                console.log("remove accepted");
                this.acceptRemove = false;
            } else {
                console.log("remove declined");
                return;
            }
            var self = this;
            var data = $(event.currentTarget).data();
            if (confirm(`Сигурни ли сте, че искате да премахнете ${`${data.fname ? data.fname : ""} ` + data.lname} от кампанията ?`)) {
        //call api to remove user
        app.api.call("get",
         app.api.buildURL("Campaigns/remove-contact-from-campaign?OAuth-Token=" + App.api.getOAuthToken() + 
         "&campaignId=" + data.campaignid +
          "&walletId=" + data.walletid + 
          "&contactId="+data.contactid),{},
          {
            success:function(result){
                getDataAndDrawHTML();
                self.acceptRemove = true;
          },
        error:function(err) {
            alert(err.message);
            self.acceptRemove = true;
        }});
    } else{
        this.acceptRemove = true;
    }
},
previewRow: function (event) {
    var data, model, success;
    success = _.bind(function (model) {
        model.module = data.module;
        app.events.trigger('preview:render', model);
    }, this);
    data = $(event.currentTarget).data();
    if (data && data.module && data.id) {
        model = app.data.createBean(data.module, { id: data.id });
        model.fetch({
            showAlerts: true,
            success: success,
            params: {
                erased_fields: true
            }
        });
    }
}
})