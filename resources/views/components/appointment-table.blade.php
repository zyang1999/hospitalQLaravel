
  <div class="row align-items-start">
    <div class="col">
      @if (count($appointments) === 0)
        <div class="alert alert-warning" role="alert">
          No Appointments
        </div>
      @else
        <table class="table" id="appointment-table">
            <thead>
              <tr>
                <th scope="col">Date</th>
                <th scope="col">Start At</th>
                <th scope="col">End At</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($appointments as $appointment)
                    <tr>
                    <th scope="row">{{$appointment->date_string}}</th>
                    <td>{{date('h:i A', strtotime($appointment->start_at))}}</td>
                    <td>{{date('h:i A', strtotime($appointment->end_at))}}</td>
                    <td>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#patientModal" data-bs-id={{ $appointment->id }}>
                            Book
                        </button>
                    </td>
                  </tr>
                @endforeach
            </tbody>
        </table>
      @endif
    </div>
  </div>
</body>
<script>
  $(document).ready( function () {
    $('#appointment-table').DataTable();
  });
</script>

