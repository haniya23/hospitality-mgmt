<style>
.button-container {
  display: flex;
  background-color: rgba(245, 73, 144);
  width: 250px;
  height: 40px;
  align-items: center;
  justify-content: space-around;
  border-radius: 10px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px,
        rgba(245, 73, 144, 0.5) 5px 10px 15px;
}

.button {
  outline: 0 !important;
  border: 0 !important;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: transparent;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  transition: all ease-in-out 0.3s;
  cursor: pointer;
}

.button:hover {
  transform: translateY(-3px);
}

.icon {
  font-size: 20px;
}
</style>

<!-- Bottom Navigation Bar -->
<div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-40 lg:hidden">
    <div class="button-container flex items-center justify-around w-80 h-12 rounded-2xl" style="background: linear-gradient(135deg, rgba(245, 73, 144, 1) 0%, rgba(138, 43, 226, 1) 100%); box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px, rgba(245, 73, 144, 0.5) 5px 10px 15px;">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1 {{ request()->routeIs('dashboard') ? 'bg-white bg-opacity-20' : '' }}">
            <i class="fas fa-home text-lg"></i>
        </a>

        <!-- Properties -->
        <a href="{{ route('properties.index') }}" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1 {{ request()->routeIs('properties.*') ? 'bg-white bg-opacity-20' : '' }}">
            <i class="fas fa-building text-lg"></i>
        </a>

        <!-- Bookings -->
        <a href="{{ route('bookings.index') }}" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1 {{ request()->routeIs('bookings.*') ? 'bg-white bg-opacity-20' : '' }}">
            <i class="fas fa-calendar text-lg"></i>
        </a>

        <!-- Customers -->
        <a href="{{ route('customers.index') }}" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1 {{ request()->routeIs('customers.*') ? 'bg-white bg-opacity-20' : '' }}">
            <i class="fas fa-users text-lg"></i>
        </a>

        <!-- Analytics -->
        <a href="{{ route('reports.analytics') }}" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1 {{ request()->routeIs('reports.*') ? 'bg-white bg-opacity-20' : '' }}">
            <i class="fas fa-chart-bar text-lg"></i>
        </a>

        <!-- More -->
        <button @click="$dispatch('toggle-sidebar')" class="button w-10 h-10 rounded-full bg-transparent flex items-center justify-center text-white transition-all duration-300 hover:-translate-y-1">
            <i class="fas fa-ellipsis-h text-lg"></i>
        </button>
    </div>
</div>