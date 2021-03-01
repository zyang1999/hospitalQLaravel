<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('QR Code') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <label class="col-form-label">Specialties</label>
                    <select id="specialtySelect" class="form-select">
                        <option selected value="">Select a Specialty</option>
                        @foreach ($specialties as $specialty)
                            <option value='{{ $specialty }}'>{{ $specialty }}</option>
                        @endforeach
                    </select>
                    <br />
                    <div>
                        <button class="btn btn-primary" onclick="generateQRCode()">Create QR Code</button>
                    </div>
                    <br />
                    <canvas id="qr-code"></canvas>
                    <br />
                    <a id="qrCodeDownload" style="display: none" download class="btn btn-primary">Download QR Code</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    var url = "<?php echo $url; ?>";

    function generateQRCode() {
        var specialty = document.getElementById('specialtySelect').value;
        if (specialty == "") {
            alert("Please select a specialty.");
        } else {
            new QRious({
                element: document.getElementById('qr-code'),
            }).set({

                foreground: 'black',
                size: 300,
                value: JSON.stringify({
                    "url": url,
                    "specialty": specialty
                })
            });
            var canvas = document.getElementById("qr-code");
            var img = canvas.toDataURL("image/png");
            $("#qrCodeDownload").prop("href", img).show();
        }
    }
</script>
