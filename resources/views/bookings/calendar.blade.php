@extends('layouts.app')

@section('title', 'Calendar - Stay loops')

@section('header')
<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Booking Calendar</h1>
            <p class="text-gray-600">Manage your bookings and view upcoming reservations</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('booking.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                New Booking
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div x-data="bookingCalendar()" x-init="init()" class="space-y-6">
    <!-- Status Toggle Sections -->
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-center mb-6 gap-4">
            <div class="switch-button mx-auto sm:mx-0">
                <div class="switch-outer" @click="toggleStatus()">
                    <input type="checkbox" x-model="showActive">
                    <div class="button">
                        <div class="button-toggle"></div>
                        <div class="button-indicator"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center sm:justify-start sm:ml-4">
                <span class="text-sm sm:text-base font-medium text-gray-600" x-text="showActive ? 'Active Bookings' : 'Pending Bookings'"></span>
            </div>
        </div>

        <!-- Active Bookings -->
        <div x-show="showActive" x-transition class="space-y-4">
            <!-- Selected Booking Details (Above Cards) -->
            <div x-show="selectedBooking && showModal" class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Booking Details</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Property</label>
                        <p class="text-gray-900" x-text="selectedBooking?.property_name"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Guest</label>
                        <p class="text-gray-900" x-text="selectedBooking?.guest_name"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Check-in</label>
                        <p class="text-gray-900" x-text="selectedBooking?.check_in"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Check-out</label>
                        <p class="text-gray-900" x-text="selectedBooking?.check_out"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p class="text-gray-900" x-text="selectedBooking?.status"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Amount</label>
                        <p class="text-gray-900" x-text="'₹' + selectedBooking?.total_amount"></p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button @click="viewBookingDetails(selectedBooking)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        View Full Details
                    </button>
                    <button @click="cancelBooking(selectedBooking)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cancel Booking
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="booking in activeBookings" :key="booking.id">
                    <div class="card cursor-pointer" @click="showBookingDetails(booking)">
                        <div class="wave" :style="'fill: ' + getStatusColor(booking.status, true) + ';'">
                            <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0,10 Q25,0 50,10 T100,10" fill="currentColor" opacity="0.3"/>
                            </svg>
                        </div>
                        <div class="icon-container" :style="'background-color: ' + getStatusColor(booking.status, true) + ';'">
                            <i :class="getStatusIcon(booking.status)" :style="'color: ' + getStatusColor(booking.status, false) + ';'" class="icon"></i>
                        </div>
                        <div class="message-text-container">
                            <p class="message-text" :style="'color: ' + getStatusColor(booking.status, false) + ';'" x-text="booking.property_name"></p>
                            <p class="sub-text" x-text="booking.guest_name"></p>
                            <p class="sub-text" x-text="formatDateRange(booking.check_in, booking.check_out)"></p>
                            <p class="sub-text text-xs" x-text="'Status: ' + booking.status"></p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <button @click.stop="cancelBooking(booking)" class="text-red-500 hover:text-red-700 text-xs" title="Cancel Booking">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Pending Bookings (includes cancelled) -->
        <div x-show="!showActive" x-transition class="space-y-4">
            <!-- Selected Booking Details (Above Cards) -->
            <div x-show="selectedBooking && showModal" class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Booking Details</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Property</label>
                        <p class="text-gray-900" x-text="selectedBooking?.property_name"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Guest</label>
                        <p class="text-gray-900" x-text="selectedBooking?.guest_name"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Check-in</label>
                        <p class="text-gray-900" x-text="selectedBooking?.check_in"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Check-out</label>
                        <p class="text-gray-900" x-text="selectedBooking?.check_out"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <p class="text-gray-900" x-text="selectedBooking?.status"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Total Amount</label>
                        <p class="text-gray-900" x-text="'₹' + selectedBooking?.total_amount"></p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button @click="viewBookingDetails(selectedBooking)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        View Full Details
                    </button>
                    <template x-if="selectedBooking?.status === 'pending'">
                        <button @click="confirmBooking(selectedBooking)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Confirm Booking
                        </button>
                    </template>
                    <template x-if="selectedBooking?.status !== 'cancelled'">
                        <button @click="cancelBooking(selectedBooking)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Cancel Booking
                        </button>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <template x-for="booking in [...pendingBookings, ...cancelledBookings]" :key="booking.id">
                    <div class="card cursor-pointer" @click="showBookingDetails(booking)">
                        <div class="wave" :style="'fill: ' + getStatusColor(booking.status, true) + ';'">
                            <svg viewBox="0 0 100 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0,10 Q25,0 50,10 T100,10" fill="currentColor" opacity="0.3"/>
                            </svg>
                        </div>
                        <div class="icon-container" :style="'background-color: ' + getStatusColor(booking.status, true) + ';'">
                            <i :class="getStatusIcon(booking.status)" :style="'color: ' + getStatusColor(booking.status, false) + ';'" class="icon"></i>
                        </div>
                        <div class="message-text-container">
                            <p class="message-text" :style="'color: ' + getStatusColor(booking.status, false) + ';'" x-text="booking.property_name"></p>
                            <p class="sub-text" x-text="booking.guest_name"></p>
                            <p class="sub-text" x-text="formatDateRange(booking.check_in, booking.check_out)"></p>
                            <p class="sub-text text-xs" x-text="'Status: ' + booking.status"></p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <template x-if="booking.status === 'pending'">
                                <button @click.stop="confirmBooking(booking)" class="text-green-500 hover:text-green-700 text-xs" title="Confirm Booking">
                                    <i class="fas fa-check"></i>
                                </button>
                            </template>
                            <template x-if="booking.status !== 'cancelled'">
                                <button @click.stop="cancelBooking(booking)" class="text-red-500 hover:text-red-700 text-xs" title="Cancel Booking">
                                    <i class="fas fa-times"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h3 class="text-lg font-semibold text-gray-900 text-center sm:text-left">Calendar View</h3>
            <div class="flex items-center justify-center gap-4">
                <button @click="previousMonth()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-chevron-left text-gray-600"></i>
                </button>
                <span class="text-base sm:text-lg font-medium text-gray-900 min-w-[180px] sm:min-w-[200px] text-center" x-text="currentMonthName + ' ' + currentYear"></span>
                <button @click="nextMonth()" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    <i class="fas fa-chevron-right text-gray-600"></i>
                </button>
            </div>
        </div>
        
        <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-4">
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Sun</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Mon</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Tue</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Wed</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Thu</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Fri</div>
            <div class="text-center text-xs sm:text-sm font-medium text-gray-500 py-1 sm:py-2">Sat</div>
        </div>
        
        <div class="grid grid-cols-7 gap-1 sm:gap-2">
            <template x-for="day in calendarDays" :key="day.date">
                <div class="min-h-[60px] sm:min-h-[80px] border border-gray-200 rounded-lg p-1 sm:p-2 relative cursor-pointer hover:bg-gray-50 transition-colors" 
                     :class="{
                        'bg-green-50 border-green-200': day.hasBookings,
                        'text-gray-400': !day.isCurrentMonth,
                        'text-gray-900': day.isCurrentMonth,
                        'bg-blue-50': day.date === new Date().toISOString().split('T')[0]
                     }"
                     @click="selectDate(day.date)">
                    <div class="text-xs sm:text-sm font-medium" 
                         :class="day.date === new Date().toISOString().split('T')[0] ? 'text-blue-600 font-bold' : ''" 
                         x-text="day.day"></div>
                    <div x-show="day.hasBookings" class="mt-1">
                        <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-500 rounded-full"></div>
                    </div>
                    <div x-show="day.date === new Date().toISOString().split('T')[0]" class="mt-1">
                        <div class="text-xs text-blue-600 font-medium hidden sm:block">Today</div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Month Navigation Info -->
        <div class="mt-4 text-center text-sm text-gray-500">
            <span>Showing bookings from </span>
            <span x-text="getMonthRangeText()"></span>
        </div>
    </div>
</div>


<style>
/* Switch Button Styles from Uiverse.io by Admin12121 */
.switch-button {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    align-items: center;
    -webkit-box-pack: center;
    justify-content: center;
    justify-content: center;
    margin: auto;
    height: 55px;
}

.switch-button .switch-outer {
    height: 100%;
    background: #252532;
    width: 115px;
    border-radius: 165px;
    -webkit-box-shadow: inset 0px 5px 10px 0px #16151c, 0px 3px 6px -2px #403f4e;
    box-shadow: inset 0px 5px 10px 0px #16151c, 0px 3px 6px -2px #403f4e;
    border: 1px solid #32303e;
    padding: 6px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.switch-button .switch-outer input[type="checkbox"] {
    opacity: 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    position: absolute;
}

.switch-button .switch-outer .button-toggle {
    height: 42px;
    width: 42px;
    background: -webkit-gradient(
        linear,
        left top,
        left bottom,
        from(#3b3a4e),
        to(#272733)
    );
    background: -o-linear-gradient(#3b3a4e, #272733);
    background: linear-gradient(#3b3a4e, #272733);
    border-radius: 100%;
    -webkit-box-shadow: inset 0px 5px 4px 0px #424151, 0px 4px 15px 0px #0f0e17;
    box-shadow: inset 0px 5px 4px 0px #424151, 0px 4px 15px 0px #0f0e17;
    position: relative;
    z-index: 2;
    -webkit-transition: left 0.3s ease-in;
    -o-transition: left 0.3s ease-in;
    transition: left 0.3s ease-in;
    left: 0;
}

.switch-button
    .switch-outer
    input[type="checkbox"]:checked
    + .button
    .button-toggle {
    left: 58%;
}

.switch-button
    .switch-outer
    input[type="checkbox"]:checked
    + .button
    .button-indicator {
    -webkit-animation: indicator 1s forwards;
    animation: indicator 1s forwards;
}

.switch-button .switch-outer .button {
    width: 100%;
    height: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    position: relative;
    -webkit-box-pack: justify;
    justify-content: space-between;
}

.switch-button .switch-outer .button-indicator {
    height: 25px;
    width: 25px;
    top: 50%;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    border-radius: 50%;
    border: 3px solid #ef565f;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    right: 10px;
    position: relative;
}

@-webkit-keyframes indicator {
    30% {
        opacity: 0;
    }

    0% {
        opacity: 1;
    }

    100% {
        opacity: 1;
        border: 3px solid #60d480;
        left: -68%;
    }
}

@keyframes indicator {
    30% {
        opacity: 0;
    }

    0% {
        opacity: 1;
    }

    100% {
        opacity: 1;
        border: 3px solid #60d480;
        left: -68%;
    }
}

/* Card Styles from Uiverse.io */
.card {
    width: 100%;
    height: 80px;
    border-radius: 8px;
    box-sizing: border-box;
    padding: 10px 15px;
    background-color: #ffffff;
    box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 15px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: rgba(149, 157, 165, 0.3) 0px 12px 32px;
}

.wave {
    position: absolute;
    transform: rotate(90deg);
    left: -31px;
    top: 32px;
    width: 80px;
    fill: #04e4003a;
}

.icon-container {
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #04e40048;
    border-radius: 50%;
    margin-left: 8px;
}

.icon {
    width: 17px;
    height: 17px;
    color: #269b24;
}

.message-text-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    flex-grow: 1;
}

.message-text,
.sub-text {
    margin: 0;
    cursor: default;
}

.message-text {
    color: #269b24;
    font-size: 17px;
    font-weight: 700;
}

.sub-text {
    font-size: 14px;
    color: #555;
}
</style>

<script>
function bookingCalendar() {
    return {
        activeTab: 'active',
        showActive: false, // Controls the toggle switch state
        showModal: false,
        selectedBooking: null,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        activeBookings: [
            {
                id: 1,
                property_name: 'Mountain View Resort',
                guest_name: 'John Doe',
                check_in: '2024-01-15',
                check_out: '2024-01-18',
                status: 'confirmed'
            },
            {
                id: 2,
                property_name: 'City Hotel Downtown',
                guest_name: 'Jane Smith',
                check_in: '2024-01-20',
                check_out: '2024-01-22',
                status: 'confirmed'
            }
        ],
        pendingBookings: [
            {
                id: 3,
                property_name: 'Beachside Villa',
                guest_name: 'Mike Johnson',
                check_in: '2024-01-25',
                check_out: '2024-01-28',
                status: 'pending'
            }
        ],
        cancelledBookings: [
            {
                id: 4,
                property_name: 'Forest Cabin',
                guest_name: 'Sarah Wilson',
                check_in: '2024-01-10',
                check_out: '2024-01-12',
                status: 'cancelled'
            }
        ],
        calendarDays: [],

        init() {
            this.generateCalendar();
            this.loadBookings();
        },

        generateCalendar() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());
            
            this.calendarDays = [];
            
            // Generate calendar for current month only (6 weeks to fill the grid)
            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);
                
                // Check if this date has any bookings
                const dateStr = date.toISOString().split('T')[0];
                const hasBookings = this.hasBookingsOnDate(dateStr);
                
                this.calendarDays.push({
                    date: dateStr,
                    day: date.getDate(),
                    hasBookings: hasBookings,
                    isCurrentMonth: date.getMonth() === this.currentMonth,
                    isFutureMonth: date.getMonth() > this.currentMonth,
                    isPastMonth: date.getMonth() < this.currentMonth
                });
            }
        },

        async loadBookings() {
            try {
                const response = await fetch('/api/bookings');
                const data = await response.json();
                
                // The API returns { pending: [...], active: [...] }
                // Transform the data to match our expected format
                this.pendingBookings = data.pending.map(booking => ({
                    id: booking.id,
                    uuid: booking.uuid,
                    property_name: booking.accommodation?.property?.name || 'Unknown Property',
                    guest_name: booking.guest?.name || 'Unknown Guest',
                    check_in: booking.check_in_date,
                    check_out: booking.check_out_date,
                    status: booking.status,
                    confirmation_number: booking.confirmation_number,
                    total_amount: booking.total_amount,
                    advance_paid: booking.advance_paid
                }));
                
                this.activeBookings = data.active.map(booking => ({
                    id: booking.id,
                    uuid: booking.uuid,
                    property_name: booking.accommodation?.property?.name || 'Unknown Property',
                    guest_name: booking.guest?.name || 'Unknown Guest',
                    check_in: booking.check_in_date,
                    check_out: booking.check_out_date,
                    status: booking.status,
                    confirmation_number: booking.confirmation_number,
                    total_amount: booking.total_amount,
                    advance_paid: booking.advance_paid
                }));
                
                // Add cancelled bookings if they exist in the API response
                if (data.cancelled) {
                    this.cancelledBookings = data.cancelled.map(booking => ({
                        id: booking.id,
                        uuid: booking.uuid,
                        property_name: booking.accommodation?.property?.name || 'Unknown Property',
                        guest_name: booking.guest?.name || 'Unknown Guest',
                        check_in: booking.check_in_date,
                        check_out: booking.check_out_date,
                        status: booking.status,
                        confirmation_number: booking.confirmation_number,
                        total_amount: booking.total_amount,
                        advance_paid: booking.advance_paid
                    }));
                }
                
                // Regenerate calendar with actual booking data
                this.generateCalendar();
            } catch (error) {
                console.error('Error loading bookings:', error);
                // Keep the demo data if API fails
            }
        },

        showBookingDetails(booking) {
            this.selectedBooking = booking;
            this.showModal = true;
        },

        selectDate(date) {
            // Filter bookings for the selected date
            const allBookings = [...this.activeBookings, ...this.pendingBookings, ...this.cancelledBookings];
            const bookingsOnDate = allBookings.filter(booking => {
                const checkIn = new Date(booking.check_in);
                const checkOut = new Date(booking.check_out);
                const selectedDate = new Date(date);
                
                return selectedDate >= checkIn && selectedDate < checkOut;
            });
            
            if (bookingsOnDate.length > 0) {
                // Show the first booking's details
                this.showBookingDetails(bookingsOnDate[0]);
            } else {
                console.log('No bookings found for date:', date);
            }
        },

        viewBookingDetails(booking) {
            // Navigate to the specific booking details page using UUID
            window.location.href = `/bookings/${booking.uuid}`;
        },

        toggleStatus() {
            this.showActive = !this.showActive;
        },

        confirmBooking(booking) {
            // Implementation for confirming a booking
            console.log('Confirming booking:', booking);
            
            // Update booking status via API using UUID
            fetch(`/api/bookings/${booking.uuid}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: 'confirmed' })
            })
            .then(response => response.json())
            .then(data => {
                // Remove from pending and add to active
                this.pendingBookings = this.pendingBookings.filter(b => b.uuid !== booking.uuid);
                booking.status = 'confirmed';
                this.activeBookings.push(booking);
                
                // Regenerate calendar
                this.generateCalendar();
                
                console.log('Booking confirmed successfully');
            })
            .catch(error => {
                console.error('Error confirming booking:', error);
            });
        },

        cancelBooking(booking) {
            // Implementation for cancelling a booking
            console.log('Cancelling booking:', booking);
            
            // Prompt for cancellation reason
            const reason = prompt('Please enter the reason for cancellation:');
            if (!reason || !reason.trim()) {
                return; // User cancelled or didn't provide a reason
            }
            
            // Update booking status via API using UUID
            fetch(`/api/bookings/${booking.uuid}/cancel`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    reason: reason.trim(),
                    status: 'cancelled' 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from current list and add to cancelled
                    this.activeBookings = this.activeBookings.filter(b => b.uuid !== booking.uuid);
                    this.pendingBookings = this.pendingBookings.filter(b => b.uuid !== booking.uuid);
                    booking.status = 'cancelled';
                    this.cancelledBookings.push(booking);
                    
                    // Regenerate calendar
                    this.generateCalendar();
                    
                    console.log('Booking cancelled successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error cancelling booking:', error);
                alert('An error occurred while cancelling the booking.');
            });
        },

        hasBookingsOnDate(dateStr) {
            const allBookings = [...this.activeBookings, ...this.pendingBookings, ...this.cancelledBookings];
            return allBookings.some(booking => {
                const checkIn = new Date(booking.check_in);
                const checkOut = new Date(booking.check_out);
                const date = new Date(dateStr);
                
                return date >= checkIn && date < checkOut;
            });
        },

        getStatusColor(status, isBackground = false) {
            const colors = {
                'confirmed': isBackground ? '#04e40048' : '#269b24',
                'checked_in': isBackground ? '#3b82f648' : '#1d4ed8',
                'active': isBackground ? '#04e40048' : '#269b24',
                'pending': isBackground ? '#fbbf2448' : '#d97706',
                'cancelled': isBackground ? '#ef444448' : '#dc2626',
                'checked_out': isBackground ? '#8b5cf648' : '#7c3aed'
            };
            return colors[status] || colors['pending'];
        },

        getStatusIcon(status) {
            const icons = {
                'confirmed': 'fas fa-check-circle',
                'checked_in': 'fas fa-key',
                'active': 'fas fa-check-circle',
                'pending': 'fas fa-clock',
                'cancelled': 'fas fa-times-circle',
                'checked_out': 'fas fa-door-open'
            };
            return icons[status] || icons['pending'];
        },

        formatDateRange(checkIn, checkOut) {
            const checkInDate = new Date(checkIn);
            const checkOutDate = new Date(checkOut);
            
            const formatDate = (date) => {
                return date.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric',
                    year: date.getFullYear() !== new Date().getFullYear() ? 'numeric' : undefined
                });
            };
            
            return `${formatDate(checkInDate)} - ${formatDate(checkOutDate)}`;
        },

        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
            this.generateCalendar();
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
            this.generateCalendar();
        },

        getMonthRangeText() {
            const today = new Date();
            const currentDate = new Date(this.currentYear, this.currentMonth);
            const pastDate = new Date(today.getFullYear(), today.getMonth() - 3);
            const futureDate = new Date(today.getFullYear(), today.getMonth() + 3);
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            
            if (currentDate < pastDate) {
                return 'past months (limited to 3 months back)';
            } else if (currentDate > futureDate) {
                return 'future months (limited to 3 months ahead)';
            } else {
                return 'past and future bookings';
            }
        },

        get currentMonthName() {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            return monthNames[this.currentMonth];
        }
    }
}
</script>
@endsection
