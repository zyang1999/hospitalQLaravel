<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff List') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                        View
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
            <div id="spinner" class="spinner-border m-5" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div id="modal">
                <div class="modal-header">
                    <img class="img-thumbnail">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">First Name:</label>
                            <label for="recipient-name" class="col-form-label" id="first-name"></label>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Last Name:</label>
                            <label for="recipient-name" class="col-form-label" id="last-name"></label>
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Email:</label>
                            <label for="recipient-name" class="col-form-label" id="email"></label>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Telephone:</label>
                            <label for="recipient-name" class="col-form-label" id="telephone"></label>
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">IC:</label>
                            <label for="recipient-name" class="col-form-label" id="ic"></label>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Gender:</label>
                            <label for="recipient-name" class="col-form-label" id="gender"></label>
                        </div>
                    </div>
                    <div class="row align-items-start">
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Role</label>
                            <label for="recipient-name" class="col-form-label" id="role"></label>
                        </div>
                        <div class="col">
                            <label for="recipient-name" class="col-form-label">Specialty:</label>
                            <label for="recipient-name" class="col-form-label" id="specialty"></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Remove</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Edit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
          </div>
        </div>
      </div>
</x-app-layout>

<script>
    var exampleModal = document.getElementById('exampleModal')
    exampleModal.addEventListener('show.bs.modal', function (event) {
        // Button that triggered the modal
        var button = event.relatedTarget
        // Extract info from data-bs-* attributes
        var id = button.getAttribute('data-bs-id');
        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        // var modalTitle = exampleModal.querySelector('.modal-title')
        var modalBody = exampleModal.querySelector('.modal-body')
        var modalBody = exampleModal.querySelector('.modal-body')
        var modalImage = exampleModal.querySelector('.modal-header img')
        var firstName = document.getElementById("first-name")
        var lastName = document.getElementById("last-name") 
        var firstName = document.getElementById("first-name")
        var firstName = document.getElementById("first-name")
        var firstName = document.getElementById("first-name")
        var spinner = document.getElementById("spinner");
        var modal = document.getElementById("modal");
        // alert(staff.id);
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                modal.style.display = '';
                spinner.style.display = 'none'
                var response = JSON.parse(this.responseText).user;
                modalImage.setAttribute('src', response.selfie) 
                firstName.innerHTML = response.first_name
                lastName.innerHTML = response.last_name
            }else{
               modal.style.display = 'none';
            }
        };
        xhttp.open("GET", "./user/" + id , true);
        xhttp.send();
        
        // modalBodyInput.value = recipient
    })
</script>