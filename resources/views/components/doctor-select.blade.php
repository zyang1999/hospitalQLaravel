<label for="exampleFormControlInput1" class="form-label">Doctors</label>
<select class="form-select" aria-label="Default select example" name="doctorId" id ="doctorSelect" required>
    <option selected value="">Select a doctor</option>
    @foreach ($doctors as $doctor)
        <option value={{$doctor->id}}>Dr. {{$doctor->full_name}}</option>
    @endforeach
</select>
