<div class="flex items-center justify-center p-4 border border-gray-300 rounded-lg dark:border-gray-700">
@if ($getRecord()->codigo_qr)
         <a href="{{ asset('storage/' . $getRecord()->codigo_qr) }}" target="_blank" title="Haz clic para ver o descargar">
             <img src="{{ asset('storage/' . $getRecord()->codigo_qr) }}" alt="Código QR para {{ $getRecord()->nombre }}" class="h-40 w-40">
         </a>
@else
         <p class="text-gray-500">El código QR se generará al guardar.</p>
@endif
 </div>
