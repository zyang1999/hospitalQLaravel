<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Queue') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div id="creationAlert" class="alert alert-primary alert-dismissible fade show" style="display: none" role="alert">
                        Queue is created successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <form id="queueForm" action="/createQueue">
                        @csrf
                        <div class="row align-items-start">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Specialty</label>
                                    <select class="form-select" aria-label="Default select example" name="specialty" id ="specialtySelect" required>
                                        <option selected value="">Selected a Specialty</option>
                                        @foreach ($specialties as $specialty)
                                            <option value={{$specialty}}>{{$specialty}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col d-flex flex-row align-items-end">
                                <div class="mb-3 flex-fill">
                                    <label for="exampleFormControlInput1" class="form-label">Patient IC</label>
                                    <input type="number" class="form-control" id="ic" name="ic" required>
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
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>                            
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Patient Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
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
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="Male" value="Male" name="gender" checked>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                        Male
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="Female" name="gender"value="Female" >
                                        <label class="form-check-label" for="flexRadioDefault2">
                                        Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>             
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Patient Home Address</label>
                            <textarea type="text" class="form-control" id="homeAddress" name="homeAddress" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Patient Concern (Optional)</label>
                            <textarea type="text" class="form-control" id="concern" name="concern"></textarea>
                        </div>
                        <div class=" d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Create Queue</button>
                        </div>    
                    </form>     
                </div>
            </div>
        </div>
    </div>

    <div id="queueNoModal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Queue is Created Successfully!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                        <div class="modal-body" id="ticket">
                            <h4 class="text-center">Queue Number</h4>
                            <h1 class="text-center" id="queueNo">
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"  onclick="printElem('ticket')">Print Ticket</button>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>

<script>
    function searchPatient(){
        var IC = document.getElementById("ic").value;
        var firstName = document.getElementById("firstName");
        var lastName = document.getElementById("lastName");
        var telephone = document.getElementById("telephone");
        var homeAddress = document.getElementById("homeAddress");
        if(IC == ''){
            alert('Please enter an IC number before searching')
        }
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var patient = JSON.parse(this.responseText).patient;
                if (patient == null){
                    alert('Patient not found!');
                    firstName.value = null;
                    lastName.value = null;
                    telephone.value = null;
                    homeAddress.value = null;
                    $("input[name=gender][value=Male]").prop('checked', true);
                    
                    firstName.disabled = false;
                    lastName.disabled = false;
                    telephone.disabled = false;
                    homeAddress.disabled = false;
                    document.getElementById('Male').disabled = false;
                    document.getElementById('Female').disabled = false;
                }else{
                    firstName.value = patient.first_name;
                    lastName.value = patient.last_name;
                    telephone.value = patient.telephone;
                    homeAddress.value = patient.home_address;
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

    $("#queueForm").submit(function(e) {    

        e.preventDefault(); 

        var form = $("#queueForm");
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: $("form").serialize(), 
            success: function(data)
            {
                $("#queueNoModal").modal("show");
                document.getElementById("queueNo").innerHTML = data.queue.queue_no
            }
        });
    });

    function printElem(elem)
    {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write(document.getElementById(elem).innerHTML);

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>