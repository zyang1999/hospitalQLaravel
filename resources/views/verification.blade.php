<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Unverified Patients List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table" id="usersTable">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">First</th>
                                <th scope="col">Last</th>
                                <th scope="col">Email</th>
                                <th scope="col">IC</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($patients as $patient)
                                <tr>
                                    <th scope="row">{{ $patient->id }}</th>
                                    <td>{{ $patient->first_name }}</td>
                                    <td>{{ $patient->last_name }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ $patient->IC_no }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" data-bs-id={{ $patient->id }}>
                                            Review
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
                        <h4>Profile Details</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img id="selfie" class="img-thumbnail w-auto">
                        <img id="IC_image" class="img-thumbnail w-auto">
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">First Name:</label>
                                <input type="text" class="form-control" disabled id="first-name">
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Last Name:</label>
                                <input type="text" class="form-control" disabled id="last-name">
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Email:</label>
                                <input type="text" class="form-control" disabled id="email">
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Telephone:</label>
                                <input type="text" class="form-control" disabled id="telephone">
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">IC:</label>
                                <input type="text" class="form-control" disabled id="ic">
                            </div>
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Gender:</label>
                                <input type="text" class="form-control" disabled id="gender">
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <label for="recipient-name" class="col-form-label">Home Address:</label>
                                <textarea type="text" class="form-control" disabled id="homeAddress"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="approve()"
                            id="approveButton">Approve</button>
                        <button type="button" class="btn btn-danger" onclick="reject()"
                            id="rejectButton">Reject</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="closeButton">Close</button>
                        <div id="loading" style="display: none">
                            <div class="d-flex align-items-center">
                                <strong>Loading...</strong>
                                <div class="spinner-border ms-auto" role="status" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    var exampleModal = document.getElementById('exampleModal')
    var rejectButton = document.getElementById("rejectButton");
    var closeButton = document.getElementById("closeButton");
    var approveButton = document.getElementById("approveButton");
    var loading = document.getElementById("loading");
    var approveAlert = document.getElementById("approveAlert");
    var rejectAlert = document.getElementById("rejectAlert");

    exampleModal.addEventListener('show.bs.modal', function(event) {

        var button = event.relatedTarget
        var id = button.getAttribute('data-bs-id');

        var modalBody = exampleModal.querySelector('.modal-body')
        var modalBody = exampleModal.querySelector('.modal-body')
        var selfie = document.getElementById("selfie");
        var IC_image = document.getElementById("IC_image");
        var firstName = document.getElementById("first-name")
        var lastName = document.getElementById("last-name")
        var email = document.getElementById("email")
        var telephone = document.getElementById("telephone")
        var ic = document.getElementById("ic")
        var gender = document.getElementById("gender")
        var homeAddress = document.getElementById("homeAddress");
        var spinner = document.getElementById("spinner");
        var modal = document.getElementById("modal");

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                modal.style.display = '';
                spinner.style.display = 'none';
                var response = JSON.parse(this.responseText).user;
                selfie.setAttribute('src', response.selfie)
                IC_image.setAttribute('src', response.IC_image)
                firstName.value = response.first_name;
                lastName.value = response.last_name;
                email.value = response.email;
                telephone.value = response.telephone;
                ic.value = response.IC_no;
                gender.value = response.gender;
                homeAddress.value = response.home_address;
                document.getElementById("approveButton").value = response.id;
                document.getElementById("rejectButton").value = response.id;

            } else {
                modal.style.display = 'none';
                spinner.style.display = '';
            }
        };
        xhttp.open("GET", "./user/" + id, true);
        xhttp.send();
    })

    function approve() {
        approveButton.style.display = 'none';
        rejectButton.style.display = 'none';
        closeButton.style.display = 'none';
        loading.style.display = '';
        $.post("./approveAccount", {
            "_token": "{{ csrf_token() }}",
            'id': approveButton.value
        }, function(data) {
            alert(data.message);
            $("#exampleModal").modal('hide');
            loading.style.display = 'none';
            location.reload();
        });
    }

    function reject() {
        approveButton.style.display = 'none';
        rejectButton.style.display = 'none';
        closeButton.style.display = 'none';
        loading.style.display = '';
        $.post("./rejectAccount", {
            "_token": "{{ csrf_token() }}",
            'id': rejectButton.value
        }, function(data) {
            alert(data.message);
            $("#exampleModal").modal('hide');
            loading.style.display = 'none';
            location.reload();
        });
    }

    $(document).ready(function() {
        $('#usersTable').DataTable();
    });

</script>
