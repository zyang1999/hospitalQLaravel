<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row mb-3">
                <div class="col">
                    <div class="card text-center" >
                        <div class="card-body">
                            <h5 class="card-title">Number of Queues Today</h5>
                            <h3>{{$queueCount}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Number of Patients Waiting for Verification</h5>
                            <h3>{{$userCount}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Queue Statistic
                    <div id="queue-chart" style="height: 300px;"></div>
                    Appointment Statistic
                    <div id="appointment-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const queueChart = new Chartisan({
            el: '#queue-chart',
            url: "@chart('queue_chart')",
            hooks: new ChartisanHooks()
                .beginAtZero()
                .colors()
                .borderColors()
                .datasets([{ type: 'line', fill: false }, { type: 'line', fill: false }]),
        });
        const appointmentChart = new Chartisan({
            el: '#appointment-chart',
            url: "@chart('appointment_chart')",
            hooks: new ChartisanHooks()
                .beginAtZero()
                .colors()
                .borderColors()
                .datasets([{ type: 'line', fill: false }, { type: 'line', fill: false }]),
        });
        
    </script>
</x-app-layout>