import { Component, OnInit, ElementRef } from '@angular/core';
import { Globals } from '.././globals';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { AnnouncementService } from '../services/announcement.service';
import { ActivedeleteService } from '../services/activedelete.service';
declare function myInput() : any;
declare var $,swal: any;

@Component({
  selector: 'app-announcementtypelist',
  templateUrl: './announcementtypelist.component.html',
  styleUrls: ['./announcementtypelist.component.css']
})
export class AnnouncementtypelistComponent implements OnInit {
  announcementtypeEntity;
  submitted;
  btn_disable;
  typelist;
  deleteEntity;
  header;

  constructor( public globals: Globals, private router: Router,private elem: ElementRef, private route: ActivatedRoute,
		private AnnouncementService: AnnouncementService,private ActivedeleteService: ActivedeleteService) { }

    ngOnInit() {
      setTimeout(function(){
        // if( $(".bg_white_block").hasClass( "ps--active-y" )){  
        // 	$('footer').removeClass('footer_fixed');     
        // }      
        // else{  
        // 	$('footer').addClass('footer_fixed');    
        // }
        myInput();
      },100); 
      this.default();
    }
    default(){
      this.announcementtypeEntity = {};
      this.AnnouncementService.getAnnouncementTypes()
        .then((data) => {
          setTimeout(function(){
            var table = $('#list_tables').DataTable( {
             // scrollY: '55vh',
          responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    type: ''
                }
            },
                 scrollCollapse: true,           
                   "oLanguage": {
                     "sLengthMenu": "_MENU_ AnnouncementType per Page",
                     "sInfo": "Showing _START_ to _END_ of _TOTAL_ AnnouncementType",
                     "sInfoFiltered": "(filtered from _MAX_ total AnnouncementType)",
                     "sInfoEmpty": "Showing 0 to 0 of 0 AnnouncementType"
                   },
                   dom: 'lBfrtip',
                   buttons: [
                         
                       ]
                 });
                 
                 var buttons = new $.fn.dataTable.Buttons(table, {
                 buttons: [
                             {
                             extend: 'excel',
                             title: 'AnnouncementType List',
                             exportOptions: {
                               columns: [ 0, 1, 2 ]
                               }
                             },
                             {
                             extend: 'print',
                             title: 'AnnouncementType List',
                             exportOptions: {
                               columns: [ 0, 1, 2 ]
                               }
                             },
                         ]
               }).container().appendTo($('#buttons'));
               $('.buttons-excel').attr('data-original-title', 'Export to Excel').tooltip();
               $('.buttons-print').attr('data-original-title', 'Print').tooltip();
          },100); 
          this.typelist = data;
        //	this.globals.isLoading = false;
        },
        (error) => {
         // this.globals.isLoading = false;	
          this.router.navigate(['/pagenotfound']);
        });
    }
    isActiveChange(changeEntity, i)
  { 
    if(this.typelist[i].IsActive==1){
      this.typelist[i].IsActive = 0;
      changeEntity.IsActive = 0;
    } else {
      this.typelist[i].IsActive = 1;
      changeEntity.IsActive = 1;
    }
    this.globals.isLoading = true;
    changeEntity.UpdatedBy = this.globals.authData.UserId;
    changeEntity.Id = changeEntity.AnnouncementTypeId;
    changeEntity.TableName = 'tblannouncementtype';
    changeEntity.FieldName = 'AnnouncementTypeId';
    changeEntity.Module = 'AnnouncementType Activity';
		this.ActivedeleteService.isActiveChange(changeEntity)
		.then((data) => 
		{	      
      this.globals.isLoading = false;	
			// this.globals.message = 'Feedback Category updated successfully';
			// this.globals.type = 'success';
      // this.globals.msgflag = true;	
      swal({
			
				type: 'success',
				title: 'AnnouncementType updated successfully' ,
				showConfirmButton: false,
				timer: 1500
			  })			
		}, 
		(error) => 
		{
      this.globals.isLoading = false;
      this.router.navigate(['/pagenotfound']);
		});		
  }
  deleteAnnouncementType(type)
	{ 
		this.deleteEntity =  type;
    swal({
      title: 'Delete a Announcement Type',
      text: "Are you sure you want to delete this announcement type?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    })
    .then((result) => {
      if (result.value) {
    var del={'Userid':this.globals.authData.UserId,'id':type.AnnouncementTypeId,'TableName':'tblannouncementtype','FieldName':'AnnouncementTypeId','Module':'Announcement Type'};
		this.ActivedeleteService.delete(del)
		.then((data) => 
		{
			let index = this.typelist.indexOf(type);
			if (index != -1) {
				this.typelist.splice(index, 1);
			}	
      swal({
        type: 'success',
        title: 'Deleted!',
        text: 'AnnouncementType has been deleted successfully',
        showConfirmButton: false,
        timer: 1500
      })	
		}, 
		(error) => 
		{
			if(error.text){
				this.globals.message = "You can't delete this record because of their dependency!";
				this.globals.type = 'danger';
				this.globals.msgflag = true;
			}	
    });	
  }
  })				
	}


}
