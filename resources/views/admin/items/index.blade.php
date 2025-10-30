@extends('layouts.admin')

@section('title', __('messages.items'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">{{ __('messages.items_inventory') }}</h2>
            <p class="text-gray-600">{{ __('messages.manage_catalog') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('items.export-pdf') }}" target="_blank" class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-xl hover:from-red-600 hover:to-orange-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="bi bi-file-pdf text-xl"></i>
                <span class="font-semibold">{{ __('messages.export_pdf') }}</span>
            </a>
            <a href="{{ route('items.create') }}" class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="bi bi-plus-circle text-xl"></i>
                <span class="font-semibold">{{ __('messages.new_item') }}</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-animate bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <i class="bi bi-check-circle-fill text-xl mr-3"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-animate bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-lg">
            <div class="flex items-center">
                <i class="bi bi-exclamation-circle-fill text-xl mr-3"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="card shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table min-w-full divide-y divide-gray-200" style="width:100%">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.item') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.category') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.sku') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.price') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.stock') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="bi bi-box-seam text-2xl text-indigo-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                        @if($item->barcode)
                                            <div class="text-xs text-gray-500 font-mono mt-1">
                                                <i class="bi bi-upc-scan"></i> {{ $item->barcode }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800">
                                    {{ $item->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-semibold text-gray-900">{{ $item->sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-green-600">${{ number_format($item->price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $item->available_stock > 10 ? 'bg-green-100 text-green-800' : ($item->available_stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    <i class="bi bi-box mr-1"></i> {{ $item->available_stock }} {{ __('messages.units') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <button onclick="quickViewItem({{ $item->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="{{ __('messages.quick_view') }}">
                                        <i class="bi bi-eye text-lg"></i>
                                    </button>
                                    <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="{{ __('messages.view_details') }}">
                                        <i class="bi bi-box-arrow-up-right text-lg"></i>
                                    </a>
                                    <a href="{{ route('items.edit', $item) }}" class="text-purple-600 hover:text-purple-900 transition-colors" title="{{ __('messages.edit') }}">
                                        <i class="bi bi-pencil text-lg"></i>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('messages.delete_item_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="{{ __('messages.delete') }}">
                                            <i class="bi bi-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center text-gray-400">
                                    <i class="bi bi-box-seam text-6xl mb-4"></i>
                                    <p class="text-xl font-semibold mb-2">No items found</p>
                                    <p class="text-sm mb-4">Start by creating your first item</p>
                                    <a href="{{ route('items.create') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                        <i class="bi bi-plus-circle mr-2"></i>
                                        Create your first item
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Enhanced quick view with actual item data
    window.quickViewItem = function(itemId) {
        openModal('itemQuickViewModal');
        document.getElementById('modalContentLoader').style.display = 'block';
        document.getElementById('modalContentBody').style.display = 'none';
        
        // Fetch item details via AJAX
        fetch(`/admin/items/${itemId}`)
            .then(response => response.text())
            .then(html => {
                // Extract item data from the response (simplified - you'd parse JSON in real app)
                setTimeout(() => {
                    document.getElementById('modalContentLoader').style.display = 'none';
                    document.getElementById('modalContentBody').style.display = 'block';
                    document.getElementById('modalContentBody').innerHTML = `
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-6">
                                <h2 class="text-3xl font-extrabold text-gradient">Item Details</h2>
                                <button onclick="closeModal('itemQuickViewModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>
                            </div>
                            <div class="space-y-4">
                                <p class="text-gray-600">Loading item #${itemId} details...</p>
                                <a href="/admin/items/${itemId}" class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg transform hover:-translate-y-0.5 transition-all duration-300">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    <span class="font-semibold">View Full Details</span>
                                </a>
                            </div>
                        </div>
                    `;
                }, 300);
            })
            .catch(error => {
                console.error('Error loading item:', error);
                document.getElementById('modalContentLoader').style.display = 'none';
                document.getElementById('modalContentBody').style.display = 'block';
                document.getElementById('modalContentBody').innerHTML = `
                    <div class="p-8 text-center">
                        <i class="bi bi-exclamation-circle text-5xl text-red-500 mb-4"></i>
                        <p class="text-red-600 font-semibold">Error loading item details</p>
                    </div>
                `;
            });
    };
</script>
@endpush

