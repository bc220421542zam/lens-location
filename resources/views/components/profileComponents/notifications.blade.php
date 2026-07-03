 <div class="card chart-transition bg-[#EEEFF7] rounded-2xl p-6">
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-4">Notifications</p>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Push Notifications</p>
                                <p class="text-xs text-gray-400">Receive booking alerts in app</p>
                            </div>
                            <button @click="notifications = !notifications"
                                :class="notifications ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="notifications ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">Email Alerts</p>
                                <p class="text-xs text-gray-400">Get booking confirmations via email</p>
                            </div>
                            <button @click="emailAlerts = !emailAlerts"
                                :class="emailAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="emailAlerts ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-indigo-900">SMS Alerts</p>
                                <p class="text-xs text-gray-400">Receive SMS for new bookings</p>
                            </div>
                            <button @click="smsAlerts = !smsAlerts"
                                :class="smsAlerts ? 'bg-indigo-900' : 'bg-gray-200'"
                                class="relative w-11 h-6 rounded-full transition-colors duration-200 shrink-0">
                                <span :class="smsAlerts ? 'translate-x-5' : 'translate-x-1'"
                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 block">
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
