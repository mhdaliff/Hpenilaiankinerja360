<a class="dropdown-item border-radius-md"  wire:click="logout">
    {{-- <i class="fa fa-user me-sm-1 {{ in_array(request()->route()->getName(),['profile', 'my-profile']) ? 'text-white' : '' }}"></i> --}}
    <span class="d-sm-inline d-none text-danger {{ in_array(request()->route()->getName(),['profile', 'my-profile']) ? 'text-white' : '' }}">Keluar</span>
</a>
