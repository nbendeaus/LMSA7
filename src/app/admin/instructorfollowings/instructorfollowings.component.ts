import { Component, OnInit } from '@angular/core';
import { Globals } from '.././globals';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { InstructorfollowingsService } from '../services/instructorfollowings.service';
declare var $, swal: any;
declare function myInput(): any;
declare var $, Bloodhound: any;

@Component({
  selector: 'app-instructorfollowings',
  templateUrl: './instructorfollowings.component.html',
  styleUrls: ['./instructorfollowings.component.css']
})
export class InstructorfollowingsComponent implements OnInit {

  InstructorFollowingsList;
  //FollowerData;
  msgflag;
  constructor(public globals: Globals, private router: Router, private route: ActivatedRoute,
    private InstructorfollowingsService: InstructorfollowingsService) { }

  ngOnInit() {

    this.InstructorfollowingsService.getAllFollowings(this.globals.authData.UserId)
    .then((data) => {
      debugger
      let todaysdate = this.globals.todaysdate;
      setTimeout(function () {
        var table = $('#list_tables').DataTable({
          // scrollY: '55vh',
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.childRowImmediate,
              type: ''
            }
          },
          scrollCollapse: true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ Followers per page",
            "sInfo": "Showing _START_ to _END_ of _TOTAL_ Followers",
            "sInfoFiltered": "(filtered from _MAX_ total Followers)",
            "sInfoEmpty": "Showing 0 to 0 of 0 Followers"
          },
          dom: 'lBfrtip',
          buttons: [
            {
              extend: 'excel',
              title: 'Learning Management System – My Followers – ' + todaysdate,
              filename: 'LearningManagementSystem–MyFollowers–' + todaysdate,
              customize: function (xlsx) {
                var source = xlsx.xl['workbook.xml'].getElementsByTagName('sheet')[0];
                source.setAttribute('name', 'LMS-MyFollowers');
              },
              exportOptions: {
                columns: [0, 1, 2, 3, 4]
              }
            },
            {
              extend: 'print',
              title: 'Learning Management System – My Followers – ' + todaysdate,
              exportOptions: {
                columns: [0, 1, 2, 3, 4]
              }
            },
          ]
        });

        var buttons = new $.fn.dataTable.Buttons(table, {
          buttons: [
            {
              extend: 'excel',
              title: 'Followers List',
              exportOptions: {
                columns: [0, 1, 2, 3, 4]
              }
            },
            {
              extend: 'print',
              title: 'Followers List',
              exportOptions: {
                columns: [0, 1, 2, 3, 4]
              }
            },
          ]
        }).container().appendTo($('#buttons'));

        $('.buttons-excel').attr('data-original-title', 'Export').tooltip();
        $('.buttons-print').attr('data-original-title', 'Print').tooltip();
      }, 100);
      this.InstructorFollowingsList = data;
      console.log( this.InstructorFollowingsList);
      //this.globals.isLoading = false;	
    },
      (error) => {
        // this.globals.isLoading = false;
        this.router.navigate(['/pagenotfound']);
      });
  this.msgflag = false;

  setTimeout(function () {
    if ($(".bg_white_block").hasClass("ps--active-y")) {
      $('footer').removeClass('footer_fixed');
    }
    else {
      $('footer').addClass('footer_fixed');
    }
  }, 1000);
  }

}
