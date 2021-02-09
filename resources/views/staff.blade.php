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
                    <table class="table">
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
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-id={{ $staff->id }}>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="d-flex justify-content-center">
                <div id="spinner" class="spinner-border m-5" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div id="modal">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img class="img-thumbnail">
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">First Name:</label>
                            <input type="text" class="form-control" id="first-name">
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Last Name:</label>
                            <input type="text" class="form-control" id="last-name">
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label" >Email:</label>
                            <input type="text" class="form-control" id="email" disabled>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Telephone:</label>
                            <input type="text" class="form-control" id="telephone">
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label" >IC:</label>
                            <input type="text" class="form-control" id="ic" disabled>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Gender:</label>
                            <input type="text" class="form-control" id="gender">
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Role</label>
                            <input type="text" class="form-control" id="role">
                        </div>
                        <div class="col" id="specialty-field">
                            <label for="recipient-name" class="col-form-label">Specialty:</label>
                            <input type="text" class="form-control" id="specialty">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Remove</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="createStaffModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <label for="recipient-name" class="form-label">Upload Profile Picture</label>
                                <input type="file" name="image" id="ImageInput" onchange="toDataUrl">
                            </div>
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
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Telephone:</label>
                                <input type="number"  class="form-control" id="telephone" name="telephone" required>
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
                                <select id="roleSelect" class="form-select" aria-label="Default select example" onchange="selectRole()" name="role" required>
                                    <option selected>Select a Role</option>
                                    <option value="ADMIN">Admin</option>
                                    <option value="DOCTOR">Doctor</option>
                                    <option value="NURSE">Nurse</option>
                                </select>
                            </div>
                            <div class="col" id="createSpecialtyField" style="display: none">
                                <label for="recipient-name" class="col-form-label">Specialty:</label>
                                <input type="text" class="form-control" id="specialtyInput" name="specialty" >
                            </div>
                        </div>
                        <div class="row align-items-start mt-3">
                            <div class="col">
                                <label for="exampleFormControlInput1" class="form-label">Patient Home Address</label>
                                <textarea type="text" class="form-control" id="homeAddress" name="homeAddress" name="homeAddress" required></textarea>
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
    var exampleModal = document.getElementById('exampleModal')
    var image = {};
    exampleModal.addEventListener('show.bs.modal', function (event) {

        var button = event.relatedTarget
        var id = button.getAttribute('data-bs-id');

        var modalBody = exampleModal.querySelector('.modal-body')
        var modalBody = exampleModal.querySelector('.modal-body')
        var modalImage = exampleModal.querySelector('.modal-body img')
        var firstName = document.getElementById("first-name")
        var lastName = document.getElementById("last-name") 
        var email = document.getElementById("email")
        var telephone = document.getElementById("telephone")
        var ic = document.getElementById("ic")     
        var role = document.getElementById("role")
        var specialty = document.getElementById("specialty")   
        var spinner = document.getElementById("spinner");
        var modal = document.getElementById("modal");
        var specialtyField = document.getElementById("specialtyField");

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                modal.style.display = '';
                spinner.style.display = 'none'
                var response = JSON.parse(this.responseText).user;
                modalImage.setAttribute('src', response.selfie) 
                firstName.value = response.first_name;
                lastName.value = response.last_name;
                email.value = response.email;
                telephone.value = response.telephone;
                ic.value = response.IC_no;
                role.value = response.role;
                specialty.value = response.specialty.specialty;
                
            }else{
               modal.style.display = 'none';
               spinner.style.display = '';
            }
        };
        xhttp.open("GET", "./user/" + id , true);
        xhttp.send();
    });

    function selectRole(){
        var role = document.getElementById("roleSelect").value;
        var specialtyField = document.getElementById("createSpecialtyField");
        if(role == "DOCTOR"){
            specialtyField.style.display = '';
            document.getElementById("specialtyInput").required = true;
        }else{
            specialtyField.style.display = 'none';
            document.getElementById("specialtyInput").required = false;
        }
    }

    function toDataUrl() {
        const file = document.querySelector('input[type=file]').files[0];
        const reader = new FileReader();

        reader.addEventListener("load", function () {
            image["image"] = reader.result;
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    $("#createStaffForm").submit(function(e) {    
        
        
        e.preventDefault(); 
        var reader = new FileReader();
        $.ajax({
            type: "POST",
            url: "./createStaff",
            data: $("#createStaffForm").serialize() + '&_token={{ csrf_token() }}&image=' + image.image, 
            success: function(data)
            {
                if(data.success == true){
                    alert('New Staff is created successfully!');
                }else{
                    data.message.IC_no && alert(data.message.IC_no);
                    data.message.email && alert(data.message.email);
                    data.message.email && alert(data.message.telephone);
                }
            },
        });
    });

</script>