<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form id="searchAppointmentForm">
                        @csrf
                        <div class="row align-items-start">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Specialties</label>
                                    <select class="form-select" aria-label="Default select example" name="specialty" id ="specialtySelect" required>
                                        <option selected value="">Select a specialty</option>
                                        @foreach ($specialties as $specialty)
                                            <option value='{{$specialty}}'>{{$specialty}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3" id="doctor-div">
                                    @include('/components/doctor-select')
                                </div>
                            </div>
                        </div> 
                        <div class="row align-items-start">
                            <div class="col">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary" id="search-appointment-button">Search Appointment</button> 
                                </div>  
                            </div>
                        </div>
                    </form>
                    <div id="appointment-div"></div>      
                </div>
            </div>
        </div>
    </div>

    <div id="patientModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Patient Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="patientForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row align-items-start">
                            <div class="col d-flex flex-row align-items-end">
                                <div class="mb-3 flex-fill">
                                    <label for="exampleFormControlInput1" class="form-label">Patient IC</label>
                                    <input type="number" class="form-control" id="ic" name="IC_no" required>
                                </div>
                                <div class="mb-3 ms-3">
                                    <button type="button" class="btn btn-primary" onclick="searchPatient()">Search Patient</button> 
                                </div>
                            </div> 
                        </div>
                        <div class="row align-items-start">  
                            <div class="col">
                                <div class="mb-3 ">
                                    <label for="exampleFormControlInput1" class="form-label">Patient First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" required>
                                </div>                            
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Patient Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-start">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Patient Telephone</label>
                                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                                </div>                            
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Patient Gender</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="Male" value="Male" name="gender" checked>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                        Male
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="Female" name="gender" value="Female" >
                                        <label class="form-check-label" for="flexRadioDefault2">
                                        Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>             
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Patient Home Address</label>
                            <textarea type="text" class="form-control" id="homeAddress" name="home_address" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Patient Concern (Optional)</label>
                            <textarea type="text" class="form-control" id="concern" name="concern"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="role" id="role">
                    <input type="hidden" name="patientId" id="patientId">
                    <input type="hidden" name="appointmentId" id="appointmentId">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" >Book</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    var patientModal = document.getElementById('patientModal');

    patientModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var appointmentId = button.getAttribute('data-bs-id');
        $('#appointmentId').val(appointmentId);
        $('#role').val('PATIENT');
    });

    function searchPatient(){
        var IC = document.getElementById("ic").value;
        var firstName = document.getElementById("firstName");
        var lastName = document.getElementById("lastName");
        var telephone = document.getElementById("telephone");
        var homeAddress = document.getElementById("homeAddress");
        var patientId = document.getElementById("patientId");

        if(IC == ''){
            alert('Please enter an IC number before searching')
        }
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var patient = JSON.parse(this.responseText).patient;
                if (patient == null){
                    alert('Patient not found!');
                    firstName.disabled = false;
                    lastName.disabled = false;
                    telephone.disabled = false;
                    homeAddress.disabled = false;
                    document.getElementById('Male').disabled = false;
                    document.getElementById('Female').disabled = false;

                    firstName.value = null;
                    lastName.value = null;
                    telephone.value = null;
                    homeAddress.value = null;
                    patientId.value = null;
                    $("input[name=gender][value=Male]").prop('checked', true);

                }else{
                    firstName.value = patient.first_name;
                    lastName.value = patient.last_name;
                    telephone.value = patient.telephone;
                    homeAddress.value = patient.home_address;
                    patientId.value = patient.id;
                    $("input[name=gender][value=" + patient.gender + "]").prop('checked', true);

                    firstName.disabled = true;
                    lastName.disabled = true;
                    telephone.disabled = true;
                    homeAddress.disabled = true;
                    
                    document.getElementById('Male').disabled = true;
                    document.getElementById('Female').disabled = true;
                }
            }
        };
        xhttp.open("GET", "./getUserWithIC/" + IC , true);
        xhttp.send();
    }

    $('#patientForm').submit(function (event){
        event.preventDefault();

        $.ajax({
            method:'POST',
            url: './createAppointment',
            data: $(this).serialize()
        }).done(function (response){
            if(response.success){
                alert(response.message);
            }else{
                alert(Object.values(response.message).join("\n"));
            }
            location.reload();
        });
    })

    $('#searchAppointmentForm').submit(function (event){
        event.preventDefault();
        $('#appointment-div').load('./getAppointmentTable', $(this).serialize(), function(response) {
            $('#appointment-div').html(response);
        });
    });

    $('#specialtySelect').change(function(){           
        $('#doctor-div').load('./getDoctors', {
            specialty: $(this).val(),
            _token: '{{ csrf_token() }}'
        });
    });

    $('#doctorSelect').change(function(){
        $.get('./getDoctorSpecialty', {
            doctorId: $(this).val(),
            _token: '{{ csrf_token() }}'
        }, function (response){
            $('#specialtySelect').val(response.specialty);
        });           
    });

</script>