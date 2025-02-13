<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-5">
          {{-- <h1 style="font-weight:600;"> PieChart </h1> --}}
        </div>
        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3">

            @foreach ([1,2,3,4,5,6] as $item)

                <div wire:key="item-{{ $item }}" class="time-breakdown-chart text-center p-4 rounded-lg shadow-md flex flex-row justify-between">
                    <!-- Focus chart -->
                    <div  class="percentage-chart percentage-chart-focus-1 w-3/6">
                    <svg viewBox="0 0 36 36" style="width: 100%; height: 100%;">
                        <path class="percentage-chart-bg" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        style="fill: none; stroke: #e4e4e4; stroke-width: 4;" />
                        <path class="percentage-chart-stroke" stroke-dasharray="50, 100" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        style="fill: none; stroke: black; stroke-width: 4; stroke-linecap: round; transition: stroke-dasharray 0.6s ease;" />
                    </svg>
                    <div class="counter" style="--counter-end:50;position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 1.2em; font-weight: bold; color: black;"></div>
                    </div>
                    <div class="chart-info w-3/6 flex justify-between">
                    <!-- First Container -->
                    <div class="w-full text-center">
                        <h3 style="color: black" class="text-lg my-1">Item-1</h3>
                        <h3 style="color: black" class="text-lg font-bold m-0">50</h3>
                    </div>
                    
                    <!-- Second Container -->
                    <div class="w-full text-center">
                        <h3  style="color:rgba(0,175, 240, 1);" class="text-lg my-1">Item-2</h3>
                        <h3 style="color:rgba(0,175, 240, 1);"  class="text-lg font-bold m-0">100</h3>  
                    </div>
                    </div>
                </div>
                
            @endforeach

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
