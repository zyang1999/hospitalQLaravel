<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User List') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    Create User
                </button>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table table-striped" style="width:100%" id="staff-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">First</th>
                                <th scope="col">Last</th>
                                <th scope="col">IC</th>
                                <th scope="col">Role</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->IC_no }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" data-bs-id={{ $user->id }}>
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
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="d-flex justify-content-center">
                    <div id="spinner" class="spinner-border m-5" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="modal">
                    <form id="editUserForm">
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
                                    <label for="recipient-name" class="col-form-label">Email:</label>
                                    <input type="email" class="form-control" id="email" disabled>
                                </div>
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">Telephone:</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" required>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">IC:</label>
                                    <input type="number" class="form-control" id="ic" disabled>
                                </div>
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Gender:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="Male" value="Male"
                                            name="gender" checked>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Male
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="Female" name="gender"
                                            value="Female">
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col">
                                    <label for="recipient-name" class="col-form-label">Role</label>
                                    <select id="editRoleSelect" class="form-select" aria-label="Default select example"
                                        onchange="editSelectRole('editRoleSelect')" name="role" required>
                                        <option value="" selected>Select a Role</option>
                                        <option value="ADMIN">Admin</option>
                                        <option value="DOCTOR">Doctor</option>
                                        <option value="NURSE">Nurse</option>
                                        <option value="PATIENT">Patient</option>
                                    </select>
                                </div>
                                <div class="col" id="specialtyDiv" style="display: none">
                                    <label for="recipient-name" class="col-form-label">Specialty:</label>
                                    <input type="text" class="form-control" id="editSpecialtyInput" name="specialty">
                                </div>
                                <div class="col" id="editCounterDiv" style="display: none">
                                    <label for="recipient-name" class="col-form-label">Counter In Charged</label>
                                    <select id="editCounterSelect" class="form-select" name="counterNo">
                                        <option selected value="">Select a Counter Number</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col" id="editLocationDiv" style="display: none">
                                    <label for="recipient-name" class="col-form-label">Location:</label>
                                    <input type="text" class="form-control" id="editLocationInput" name="location">
                                </div>
                            </div>
                            <div class="row align-items-start mt-3">
                                <div class="col">
                                    <label for="exampleFormControlInput1" class="form-label">Home Address</label>
                                    <textarea type="text" class="form-control" id="homeAddress" name="homeAddress"
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="userId" name="id">
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

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>New Staff</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createUserForm">
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
                                <label for="recipient-name" class="col-form-label">Email:</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Telephone:</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">IC:</label>
                                <input type="number" class="form-control" id="ic" name="IC_no" required>
                            </div>
                            <div class="col">
                                <label for="exampleFormControlInput1" class="form-label">Gender:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="Male" value="Male" name="gender"
                                        checked>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Male
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="Female" name="gender"
                                        value="Female">
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Role</label>
                                <select id="roleSelect" class="form-select" aria-label="Default select example"
                                    onchange="createSelectRole('roleSelect')" name="role" required>
                                    <option selected value="">Select a Role</option>
                                    <option value="ADMIN">Admin</option>
                                    <option value="DOCTOR">Doctor</option>
                                    <option value="NURSE">Nurse</option>
                                    <option value="PATIENT">Patient</option>
                                </select>
                            </div>
                            <div class="col" id="createSpecialtyDiv" style="display: none">
                                <label for="recipient-name" class="col-form-label">Specialty:</label>
                                <input type="text" class="form-control" id="specialtyInput" name="specialty">
                            </div>
                            <div class="col" id="counterDiv" style="display: none">
                                <label for="recipient-name" class="col-form-label">Counter In Charged</label>
                                <select id="counterSelect" class="form-select" name="counterNo">
                                    <option selected value="">Select a Counter Number</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col" id="locationDiv" style="display: none">
                                <label for="recipient-name" class="col-form-label">Location:</label>
                                <input type="text" class="form-control" id="locationInput" name="location">
                            </div>
                        </div>
                        <div class="row align-items-start mt-3">
                            <div class="col">
                                <label for="exampleFormControlInput1" class="form-label">Home Address</label>
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
    var editUserModal = document.getElementById('editUserModal')

    editUserModal.addEventListener('show.bs.modal', function(event) {

        var button = event.relatedTarget;
        var id = button.getAttribute('data-bs-id');
        var profilePicture = editUserModal.querySelector('.modal-body img')
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

                if (response.role == 'DOCTOR') {
                    editSpecialtyInput.value = response.specialty.specialty;
                    editLocationInput.value = response.specialty.location;
                    specialtyDiv.style.display = '';
                    editLocationDiv.style.display = '';
                    editSpecialtyInput.required = true;
                    editLocationInput.required = true;
                } else {
                    specialtyDiv.style.display = 'none';
                    editLocationDiv.style.display = 'none';
                    editSpecialtyInput.required = false;
                    editLocationInput.required = false;
                }

                document.getElementById("userId").value = id;
            } else {
                modal.style.display = 'none';
                spinner.style.display = '';
            }
        };
        xhttp.open("GET", "./user/" + id, true);
        xhttp.send();
    });

    function createSelectRole() {
        var role = document.getElementById('roleSelect');
        var specialtyDiv = document.getElementById("createSpecialtyDiv");
        var specialtyInput = document.getElementById("specialtyInput");
        var locationDiv = document.getElementById("locationDiv");
        var locationInput = document.getElementById("locationInput");

        if (role.value == "DOCTOR") {
            specialtyDiv.style.display = '';
            locationDiv.style.display = '';

            specialtyInput.required = true;
            locationInput.required = true;
            $('#counterDiv').hide();
            $('#counterSelect').prop("required", false);
        } else if (role.value == "NURSE") {
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
            $('#counterDiv').show();
            $('#counterSelect').prop('required', true);
        } else {
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
            $('#counterDiv').hide();
            $('#counterSelect').prop("required", false);
        }
    }

    function editSelectRole() {
        var role = document.getElementById('editRoleSelect');
        var specialtyDiv = document.getElementById("specialtyDiv");
        var specialtyInput = document.getElementById("editSpecialtyInput");
        var locationDiv = document.getElementById("editLocationDiv");
        var locationInput = document.getElementById("editLocationInput");

        if (role.value == "DOCTOR") {
            specialtyDiv.style.display = '';
            locationDiv.style.display = '';

            specialtyInput.required = true;
            locationInput.required = true;
            $('#editCounterDiv').hide();
            $('#editCounterSelect').prop("required", false);
        } else if (role.value == "NURSE") {
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
            $('#editCounterDiv').show();
            $('#editCounterSelect').prop('required', true);
        } else {
            specialtyDiv.style.display = 'none';
            locationDiv.style.display = 'none';

            specialtyInput.required = false;
            locationInput.required = false;
            $('#editCounterDiv').hide();
            $('#editCounterSelect').prop("required", false);
        }
    }

    $("#createUserForm").submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var image = $('#imageInput')[0].files[0];
        formData.append('image', image);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            type: "POST",
            url: "./createUser",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success == true) {
                    alert('New Staff is created successfully!');
                    $("#createUserForm").modal('hide');
                    location.reload();
                } else {
                    data.message.IC_no && alert(data.message.IC_no);
                    data.message.email && alert(data.message.email);
                    data.message.telephone && alert(data.message.telephone);
                }
            },
        });
    });

    $("#editUserForm").submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            type: "POST",
            url: "./editUser",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success == true) {
                    alert(data.message);
                    location.reload();
                } else {
                    data.message.telephone && alert(data.message.telephone);
                }
            },
        });
    });

    $('#removeButton').click(function() {
        $.ajax({
            type: 'POST',
            url: "./removeUser",
            data: {
                id: $('#userId').val(),
                _token: '{{ csrf_token() }}'
            }
        }).done(function(data) {
            alert(data.message);
            location.reload();
        })
    })

    $(document).ready(function() {
        $('#staff-table').DataTable();
    });

</script>
