<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStaffModal" >
                    Create Staff
                </button>
            </div> 
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table" id="staff-table">
                        <thead>
                          <tr>
                            <th scope="col">ID</th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                            <th scope="col">Role</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($staffs as $staff)
                                <tr>
                                <th scope="row">{{$staff->id}}</th>
                                <td>{{$staff->first_name}}</td>
                                <td>{{$staff->last_name}}</td>
                                <td>{{$staff->role}}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editStaffModal" data-bs-id={{ $staff->id }}>
                                        Edit
                                    </button>
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="d-flex justify-content-center">
                    <div id="spinner" class="spinner-border m-5" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="modal">
                    <form id="editStaffForm">
                        <div class="modal-header">
                            <h3>Edit Profile</h3>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img class="img-thumbnail">
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">First Name:</label>
                                    <input type="text" class="form-control" id="first-name" name="firstName" required>
                                </div>
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">Last Name:</label>
                                    <input type="text" class="form-control" id="last-name" name="lastName" required>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label" >Email:</label>
                                    <input type="email" class="form-control" id="email" disabled>
                                </div>
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">Telephone:</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" required>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label" >IC:</label>
                                    <input type="number" class="form-control" id="ic" disabled>
                                </div>
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Gender:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="Male" value="Male" name="gender" checked>
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                Male
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="Female" name="gender"value="Female" >
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Female
                                            </label>
                                        </div>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">Role</label>
                                    <select id="editRoleSelect" class="form-select" aria-label="Default select example" onchange="editSelectRole('editRoleSelect')" name="role" required>
                                        <option value="" selected>Select a Role</option>
                                        <option value="ADMIN">Admin</option>
                                        <option value="DOCTOR">Doctor</option>
                                        <option value="NURSE">Nurse</option>
                                    </select>
                                </div>
                                <div class="col" id="specialtyDiv" style="display: none">
                                    <label for="recipient-name" class="col-form-label">Specialty:</label>
                                    <input type="text" class="form-control" id="editSpecialtyInput" name="specialty" >
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col" id="editLocationDiv" style="display: none">
                                    <label for="recipient-name" class="col-form-label">Location:</label>
                                    <input type="text" class="form-control" id="editLocationInput" name="location" >
                                </div>
                            </div>
                            <div class="row align-items-start mt-3">
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Patient Home Address</label>
                                    <textarea type="text" class="form-control" id="homeAddress" name="homeAddress" required></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="userId" name="id" >
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="removeButton">Remove</button>
                            <button type="submit" class="btn btn-primary" id="saveButton" name="id">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
                <div class="modal-header">
                    <h3>New Staff</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createStaffForm" >
                    <div class="modal-body">
                        <div class="row align-items-start">
                            <div class="mb-3">
                                <label for="formFile" class="form-label">Upload Profile Picture</label>
                                <input class="form-control" type="file" id="imageInput" accept="image/*">
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">First Name:</label>
                                <input type="text" class="form-control" name="firstName" required>
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Last Name:</label>
                                <input type="text" class="form-control" name="lastName" required>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label" >Email:</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Telephone:</label>
                                <input type="text"  class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label" >IC:</label>
                                <input type="number" class="form-control" id="ic" name="IC_no" required>
                            </div>
                            <div class="col">
                                <label for="exampleFormControlInput1" class="form-label">Gender:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="Male" value="Male" name="gender" checked>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Male
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="Female" name="gender"value="Female" >
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Role</label>
                                <select id="roleSelect" class="form-select" aria-label="Default select example" onchange="createSelectRole('roleSelect')" name="role" required>
                                    <option selected>Select a Role</option>
                                    <option value="ADMIN">Admin</option>
                                    <option value="DOCTOR">Doctor</option>
                                    <option value="NURSE">Nurse</option>
                                </select>
                            </div>
                            <div class="col" id="createSpecialtyDiv" style="display: none">
                                <label for="recipient-name" class="col-form-label">Specialty:</label>
                                <input type="text" class="form-control" id="specialtyInput" name="specialty" >
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col" id="locationDiv" style="display: none">
                                <label for="recipient-name" class="col-form-label">Location:</label>
                                <input type="text" class="form-control" id="locationInput" name="location" >
                            </div>
                        </div>
                        <div class="row align-items-start mt-3">
                            <div class="col">
                                <label for="exampleFormControlInput1" class="form-label">Patient Home Address</label>
                                <textarea type="text" class="form-control" name="homeAddress" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
</x-app-layout>

<script>
    var editStaffModal = document.getElementById('editStaffModal')

    editStaffModal.addEventListener('show.bs.modal', function (event) {

        var button = event.relatedTarget;
        var id = button.getAttribute('data-bs-id');
        var profilePicture = editStaffModal.querySelector('.modal-body img')
        var firstName = document.getElementById("first-name")
        var lastName = document.getElementById("last-name") 
        var email = document.getElementById("email")
        var telephone = document.getElementById("telephone")
        var ic = document.getElementById("ic")     
        var editRoleSelect = document.getElementById("editRoleSelect");  
        var homeAddress = document.getElementById("homeAddress");
        var specialtyDiv = document.getElementById("specialtyDiv");
        var editLocationDiv = document.getElementById("editLocationDiv"); 
        var editSpecialtyInput = document.getElementById("editSpecialtyInput");
        var editLocationInput = document.getElementById("editLocationInput");

        var spinner = document.getElementById("spinner");
        var modal = document.getElementById("modal");

        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                modal.style.display = '';
                spinner.style.display = 'none'
                
                var response = JSON.parse(this.responseText).user;

                profilePicture.setAttribute('src', response.selfie) 
                firstName.value = response.first_name;
                lastName.value = response.last_name;
                email.value = response.email;
                telephone.value = response.telephone;
                ic.value = response.IC_no;
                $("input[name=gender][value=" + response.gender + "]").prop('checked', true);
                editRoleSelect.value = response.role;
                homeAddress.value = response.home_address;

                if(response.role == 'DOCTOR'){
                    editSpecialtyInput.value = response.specialty.specialty;
                    editLocationInput.value = response.specialty.location;
                    specialtyDiv.style.display = '';
                    editLocationDiv.style.display = '';
                    editSpecialtyInput.required = true;
                    editLocationInput.required = true;
                }else{
                    specialtyDiv.style.display = 'none';
                    editLocationDiv.style.display = 'none';
                    editSpecialtyInput.required = false;
                    editLocationInput.required = false;
                }

                document.getElementById("userId").value = id;
            }else{
               modal.style.display = 'none';
               spinner.style.display = '';
            }
        };
        xhttp.open("GET", "./user/" + id , true);
        xhttp.send();
    });

    function createSelectRole(){
        var role = document.getElementById('roleSelect');
        var specialtyDiv = document.getElementById("createSpecialtyDiv");
        var specialtyInput = document.getElementById("specialtyInput");
        var locationDiv = document.getElementById("locationDiv");
        var locationInput = document.getElementById("locationInput");

        if(role.value == "DOCTOR"){
            specialtyDiv.style.display = '';
            locationDiv.style.display = '';

            specialtyInput.required = true;
            locationInput.required = true;
        }else{
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
        }
    }

    function editSelectRole(){
        var role = document.getElementById('editRoleSelect');
        var specialtyDiv = document.getElementById("specialtyDiv");
        var specialtyInput = document.getElementById("editSpecialtyInput");
        var locationDiv = document.getElementById("editLocationDiv");
        var locationInput = document.getElementById("editLocationInput");

        if(role.value == "DOCTOR"){
            specialtyDiv.style.display = '';
            locationDiv.style.display = '';

            specialtyInput.required = true;
            locationInput.required = true;
        }else{
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
        }
    }

    $("#createStaffForm").submit(function(e) {     
        e.preventDefault(); 

        var formData = new FormData(this);
        var image =  $('#imageInput')[0].files[0];
        formData.append('image', image);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            type: "POST",
            url: "./createStaff",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data)
            {
                if(data.success == true){
                    alert('New Staff is created successfully!');
                    $("#createStaffForm").modal('hide');
                    location.reload();
                }else{
                    data.message.IC_no && alert(data.message.IC_no);
                    data.message.email && alert(data.message.email);
                    data.message.telephone && alert(data.message.telephone);
                }
            },
        });
    });

    $("#editStaffForm").submit(function(e) {     
        e.preventDefault(); 

        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            type: "POST",
            url: "./editStaff",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data)
            {
                if(data.success == true){
                    alert(data.message);
                    location.reload();
                }else{
                    data.message.telephone && alert(data.message.telephone);
                }
            },
        });
    });

    $('#removeButton').click(function(){
        $.ajax({
            type:'POST',
            url: "./removeStaff",
            data: {
                id: $('#userId').val(),
                _token: '{{ csrf_token() }}'
            }
        }).done(function(data){
            alert(data.message);
            location.reload();
        })
    })

    $(document).ready( function () {
        $('#staff-table').DataTable();
    } );
</script>