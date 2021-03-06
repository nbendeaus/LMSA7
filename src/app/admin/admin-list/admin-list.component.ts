import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { Globals } from '.././globals';

import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AdminService } from '../services/admin.service';
import { UserService } from '../services/user.service';
declare var $, swal: any;
declare function myInput(): any;
declare var $, Bloodhound: any;

@Component({
  selector: 'app-admin-list',
  templateUrl: './admin-list.component.html',
  styleUrls: ['./admin-list.component.css']
})
export class AdminListComponent implements OnInit {
  userList;
  msgflag;
  message;
  type;
  deleteEntity;
  ReInviteEntity;
  constructor(public globals: Globals, private router: Router, private UserService: UserService, private AdminService: AdminService, private route: ActivatedRoute) { }

  ngOnInit() {
	  
	  $('.buttons-excel').attr('data-original-title', 'Export to Excel').tooltip();
    $('.buttons-print').attr('data-original-title', 'Print').tooltip();

    this.AdminService.getAllUser()
      .then((data) => {
        this.userList = data;
        this.globals.isLoading = false;
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
              "sLengthMenu": "_MENU_ Admins per page",
              "sInfo": "Showing _START_ to _END_ of _TOTAL_ Admins",
              "sInfoFiltered": "(filtered from _MAX_ total Admins)",
              "sInfoEmpty": "Showing 0 to 0 of 0 Admins"
            },
            dom: 'lBfrtip',
            buttons: [
              {
                extend: 'excel',
                title: 'Learning Management System – Admin List – ' + todaysdate,
                filename: 'LearningManagementSystem–AdminList–' + todaysdate,
                customize: function (xlsx) {
                  var source = xlsx.xl['workbook.xml'].getElementsByTagName('sheet')[0];
                  source.setAttribute('name', 'LMS-AdminList');
                },
                exportOptions: {
                  columns: [0, 1, 2, 3, 4]
                }
              },
              {
                extend: 'print',
                title: 'Learning Management System –  Admin List – ' + todaysdate,
                exportOptions: {
                  columns: [0, 1, 2, 3, 4]
                }
              },
            ]
          });

          $('.buttons-excel').attr('data-original-title', 'Export').tooltip();
          $('.buttons-print').attr('data-original-title', 'Print').tooltip();

          $('#dataTables-example').dataTable();
          $('#dataTables-example_filter input').addClass('input-sm');


          $(".user").addClass("selected");
        }, 100);


        this.globals.isLoading = false;
      },
        (error) => {
          this.globals.isLoading = false;

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

  isActiveChange(changeEntity, i) {
    this.globals.isLoading = true;
    if (this.userList[i].IsActive == 1) {
      this.userList[i].IsActive = 0;
      changeEntity.IsActive = 0;
    } else {
      this.userList[i].IsActive = 1;
      changeEntity.IsActive = 1;
    }
    this.globals.isLoading = true;
    changeEntity.UpdatedBy = 1;

    this.AdminService.isActiveChange(changeEntity)
      .then((data) => {
        this.globals.isLoading = false;
        swal({

          type: 'success',
          title: 'Updated!',
          text: 'Admin Updated Successfully.',
          showConfirmButton: false,
          timer: 3000
        })

      },
        (error) => {
          this.globals.isLoading = false;
          this.router.navigate(['/pagenotfound']);
        });
  }

  isActiveChangePrime(changeEntity, i) {
    this.globals.isLoading = true;
    if (this.userList[i].AdminPrime == 1) {
      this.userList[i].AdminPrime = 0;
      changeEntity.AdminPrime = 0;
    } else {
      this.userList[i].AdminPrime = 1;
      changeEntity.AdminPrime = 1;
    }
    this.globals.isLoading = true;
    changeEntity.UpdatedBy = 1;

    this.AdminService.isActiveChangePrime(changeEntity)
      .then((data) => {
        this.globals.isLoading = false;
        swal({

          type: 'success',
          title: 'Updated!',
          text: 'Admin Updated Successfully.',
          showConfirmButton: false,
          timer: 3000
        })

      },
        (error) => {
          this.globals.isLoading = false;

        });
  }


  revokeUser(user) {
    this.deleteEntity = user;
    swal({
      title: 'Delete a Admin',
      text: "Are you sure you want to delete this admin?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    })
      .then((result) => {
        if (result.value) {
          var del = { 'Userid': this.globals, 'UserId': user.UserId };
          this.globals.isLoading = true;
          this.UserService.deleteInvitation(del)
            .then((data) => {
              let index = this.userList.indexOf(user);
              swal({

                type: 'success',
                title: 'Revoked!',
                text: 'Admin Revoked successfully.',
                showConfirmButton: false,
                timer: 3000
              })
              this.AdminService.getAllUser()
                .then((data) => {
                  this.userList = data;
                },
                  (error) => {
                    this.globals.isLoading = false;
                  });
              this.globals.isLoading = false;
            },
              (error) => {
                $('#Delete_Modal').modal('hide');
                if (error.text) {
                  swal({

                    type: 'success',
                    title: "You can't delete this record because of their dependency!",
                    showConfirmButton: false,
                    timer: 3000
                  })
                }
              });
        }
      })

  }


  reinviteUser(user) {
    this.ReInviteEntity = user;
    swal({
      title: 'Re-Invite a Admin',
      text: "Are you sure you want to re-invite this admin?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    })
      .then((result) => {
        if (result.value) {
          this.globals.isLoading = true;
          this.UserService.ReInvite(user)
            .then((data) => {
              let index = this.userList.indexOf(user);
              this.userList[index].IsStatus = 0;
              $('#ReInvite_Modal').modal('hide');
              swal({
                type: 'success',
                title: 'Re-Invited!',
                text: 'Admin Re-Invited Successfully.',
                showConfirmButton: false,
                timer: 3000
              })
              this.globals.isLoading = false;
            },
              (error) => {
                $('#Delete_Modal').modal('hide');
                if (error.text) {
                  swal({

                    type: 'success',
                    title: "You can't delete this record because of their dependency!",
                    showConfirmButton: false,
                    timer: 3000
                  })
                }
              });
        }
      })

  }


  deleteUser(user) {
    this.deleteEntity = user;
    swal({
      title: 'Delete a Admin',
      text: "Are you sure want to delete this admin?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No'
    })
      .then((result) => {
        if (result.value) {
          var del = { 'Userid': this.globals, 'UserId': user.UserId, 'AddressesId': user.AddressesId };
          this.AdminService.deleteUser(del)
            .then((data) => {
              let index = this.userList.indexOf(user);
              $('#Delete_Modal').modal('hide');
              if (index != -1) {
                this.userList.splice(index, 1);
              }
              swal({

                type: 'success',
                title: 'Deleted!',
                text: 'Admin deleted successfully',
                showConfirmButton: false,
                timer: 3000
              })
            },
              (error) => {
                $('#Delete_Modal').modal('hide');
                if (error.text) {
                  swal({

                    type: 'success',
                    title: "You can't delete this record because of their dependency!",
                    showConfirmButton: false,
                    timer: 3000
                  })
                }
              });
        }
      })

  }

}
