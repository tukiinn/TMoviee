<template>
    <div class="membership-dark-v2 pt-10 pt-header ">
        <!-- Wrapper flexbox -->
        <div class="membership-flex-outer ">
            <!-- Header đặc quyền -->
            <div class="mb-4 membership-header-flex">
                <button class="btn-back-dark mb-2"><i class="fas fa-arrow-left"></i> Trở về</button>
            
                    <h2 class="vip-title-dark mb-3">Đặc quyền VIP</h2>
                    <div v-if="loading" class="flex justify-center items-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-yellow-400"></div>
                    </div>
                    <div v-else class="vip-features-row pr-vip-features justify-center md:justify-start">
                        <span class="vip-feature-dark"><i class="fas fa-check-circle text-success me-1"></i> Không quảng cáo</span>
                        <span class="vip-feature-dark"><i class="fas fa-check-circle text-success me-1"></i> Full HD/4K</span>
                        <span class="vip-feature-dark"><i class="fas fa-check-circle text-success me-1"></i> Thuyết minh/Phụ đề tiếng Việt</span>
                        <span class="vip-feature-dark"><i class="fas fa-check-circle text-success me-1"></i> Xem trên nhiều thiết bị</span>
                        <span class="vip-feature-dark"><i class="fas fa-check-circle text-success me-1"></i> Xem sớm nhất</span>
                    </div>
              
            </div>
            <!-- Block: Gói đã đăng ký -->
            <div v-if="!membershipLoading" class="mb-8">
                <div v-if="membership.active && membership.package" class="bg-gradient-to-r from-yellow-400 to-yellow-200 rounded-xl p-6 flex items-center gap-6 shadow-lg relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>
                    
                    <!-- Crown Icon -->
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-crown text-2xl text-white"></i>
                        </div>
                    </div>

                    <!-- Membership Info -->
                    <div class="flex-1 relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-xl font-bold text-gray-900">Gói hiện tại: <span class="uppercase">{{ membership.package.name }}</span></h3>
                            <span class="px-3 py-1 bg-yellow-500 text-white text-sm font-semibold rounded-full">VIP</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div class="bg-white/50 rounded-lg p-3">
                                <div class="text-sm text-gray-600">Thời gian còn lại</div>
                                <div class="text-lg font-bold text-gray-900">{{ Math.max(0, Math.round(membership.days_left)) }} ngày</div>
                            </div>
                            <div class="bg-white/50 rounded-lg p-3">
                                <div class="text-sm text-gray-600">Ngày hết hạn</div>
                                <div class="text-lg font-bold text-gray-900">{{ new Date(membership.end_at).toLocaleDateString('vi-VN') }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <i class="fas fa-info-circle"></i>
                            <span>Gói của bạn sẽ tự động gia hạn khi hết hạn</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="relative z-10">
                        <button @click="showManageModal = true" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <i class="fas fa-cog mr-2"></i>
                            Quản lý gói
                        </button>
                    </div>
                </div>
                <div v-else class="bg-gray-800 rounded-xl p-6 text-gray-300 flex items-center gap-6 shadow relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    </div>

                    <!-- Lock Icon -->
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-lock text-2xl text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 relative z-10">
                        <h3 class="text-xl font-bold mb-2">Bạn chưa đăng ký gói VIP</h3>
                        <p class="text-gray-400 mb-4">Đăng ký ngay để trải nghiệm đặc quyền không giới hạn!</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>
                                Không quảng cáo
                            </span>
                            <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>
                                Full HD/4K
                            </span>
                            <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>
                                Xem sớm nhất
                            </span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="relative z-10">
                        <button class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-crown mr-2"></i>
                            Nâng cấp VIP
                        </button>
                    </div>
                </div>
            </div>
            <!-- Danh sách gói -->
            <div class="membership-cards-dark">
                <div v-for="(pkg, idx) in packages" :key="pkg.id" class="membership-card-dark-v2" :class="cardClass(idx)">
                    <!-- Header card -->
                    <div class="card-header-dark-v2" :class="headerClass(idx)">
                        <div class="header-content-dark">
                            <div class="pkg-title-dark">{{ pkg.name }}</div>
                            <div class="pkg-price-dark">{{ formatPrice(pkg.price) }}<span class="currency-dark">đ</span></div>
                            <div class="pkg-desc-dark">{{ pkg.description }}</div>
                        </div>
                        <img v-if="pkg.image" :src="pkg.image" alt="img" class="pkg-img-dark" />
                    </div>
                    <!-- Body card -->
                    <div class="card-body-dark-v2">
                        <ul class="feature-list-dark-v2 mb-4">
                            <li v-for="(feature, index) in pkg.features" :key="index">
                                <i class="fas fa-check-circle text-success me-2"></i>{{ feature }}
                            </li>
                        </ul>
                        <button
                            class="btn btn-dark-cta-v2 w-100"
                            :class="btnClass(idx)"
                            :disabled="isPackageOwned(pkg.id)"
                            @click="handleSubscribe(pkg.id)"
                        >
                            <template v-if="isPackageOwned(pkg.id)">
                                <i class="fas fa-check-circle mr-2"></i> Đã sở hữu
                            </template>
                            <template v-else-if="isPackageExpiring(pkg.id)">
                                <i class="fas fa-clock mr-2"></i> Gia hạn gói
                            </template>
                            <template v-else>
                                Đăng ký gói {{ pkg.name }}
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="payment-modal-overlay" @click="closePaymentModal">
            <div class="payment-modal" @click.stop>
                <div class="payment-modal-header">
                    <h3 class="text-xl font-bold text-white">Thanh toán gói {{ selectedPackage?.name }}</h3>
                    <button class="close-modal" @click="closePaymentModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="payment-modal-body">
                    <!-- Duration Selection -->
                    <div class="duration-section mb-6">
                        <h4 class="text-lg font-semibold text-white mb-3">Chọn thời gian</h4>
                        <div class="duration-options">
                            <div 
                                v-for="duration in durations" 
                                :key="duration.value"
                                class="duration-option"
                                :class="{ 'selected': selectedDuration === duration.value }"
                                @click="selectDuration(duration.value)"
                            >
                                <div class="duration-label">{{ duration.label }}</div>
                                <div class="duration-price">{{ formatPrice(calculatePrice(duration.value)) }}đ</div>
                                <div class="duration-save" v-if="duration.save">Tiết kiệm {{ duration.save }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="payment-methods-section">
                        <h4 class="text-lg font-semibold text-white mb-3">Chọn phương thức thanh toán</h4>
                        <div class="payment-methods">
                            <div 
                                v-for="method in paymentMethods" 
                                :key="method.id"
                                class="payment-method"
                                :class="{ 
                                    'selected': selectedPaymentMethod === method.id,
                                    'disabled': method.disabled 
                                }"
                                @click="!method.disabled && selectPaymentMethod(method.id)"
                            >
                                <img :src="method.icon" :alt="method.name" class="payment-icon">
                                <span class="payment-name">{{ method.name }}</span>
                                <span v-if="method.note" class="payment-note">{{ method.note }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-modal-footer">
                    <div class="total-price">
                        <span class="text-gray-400">Tổng thanh toán:</span>
                        <span class="text-xl font-bold text-white">{{ formatPrice(totalPrice) }}đ</span>
                    </div>
                    <button 
                        class="btn-pay-now"
                        :disabled="!selectedPaymentMethod || !selectedDuration"
                        @click="processPayment"
                    >
                        Thanh toán ngay
                    </button>
                </div>
            </div>
        </div>

        <!-- Manage Membership Modal -->
        <div v-if="showManageModal" class="payment-modal-overlay" @click="closeManageModal">
            <div class="payment-modal" @click.stop>
                <div class="payment-modal-header">
                    <h3 class="text-xl font-bold text-white">Quản lý gói thành viên</h3>
                    <button class="close-modal" @click="closeManageModal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="payment-modal-body">
                    <!-- Current Package Info -->
                    <div v-if="membership.active && membership.package" class="current-package-info mb-6 p-4 bg-gradient-to-r from-yellow-400 to-yellow-200 rounded-lg">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="fas fa-crown text-2xl text-yellow-600"></i>
                            <h4 class="text-lg font-semibold text-gray-900">Gói hiện tại</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-600">Tên gói</div>
                                <div class="text-gray-900 font-semibold">{{ membership.package.name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Ngày hết hạn</div>
                                <div class="text-gray-900 font-semibold">{{ new Date(membership.end_at).toLocaleDateString('vi-VN') }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Thời gian còn lại</div>
                                <div class="text-gray-900 font-semibold">{{ Math.max(0, Math.round(membership.days_left)) }} ngày</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Trạng thái</div>
                                <div class="text-gray-900 font-semibold">
                                    <span class="px-2 py-1 bg-green-500 text-white rounded-full text-sm">
                                        Đang hoạt động
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Packages -->
                    <div class="active-packages mb-6">
                        <h4 class="text-lg font-semibold text-white mb-3">Các gói đang sở hữu</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div v-for="pkg in activePackages" :key="pkg.id" 
                                 class="package-option p-4 bg-gray-800 rounded-lg"
                                 :class="{ 'border-2 border-green-500': pkg.id === membership.package?.id }">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-white font-semibold">{{ pkg.name }}</div>
                                        <div class="text-gray-400 text-sm">{{ pkg.description }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-400 font-bold">{{ formatPrice(pkg.price) }}đ</div>
                                        <div class="text-sm text-gray-400">/ tháng</div>
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-400">Ngày bắt đầu</div>
                                        <div class="text-white">{{ new Date(pkg.start_at).toLocaleDateString('vi-VN') }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-400">Ngày kết thúc</div>
                                        <div class="text-white">{{ new Date(pkg.end_at).toLocaleDateString('vi-VN') }}</div>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span v-for="(feature, index) in pkg.features.slice(0, 3)" :key="index"
                                          class="px-2 py-1 bg-gray-700 rounded-full text-xs text-gray-300">
                                        {{ feature }}
                                    </span>
                                </div>
                                <div class="mt-3 flex justify-between items-center">
                                    <div class="text-sm text-gray-400">
                                        Còn lại: <span class="text-green-400 font-semibold">{{ Math.max(0, Math.round((new Date(pkg.end_at) - new Date()) / (1000 * 60 * 60 * 24))) }} ngày</span>
                                    </div>
                                    <button v-if="Math.round((new Date(pkg.end_at) - new Date()) / (1000 * 60 * 60 * 24)) <= 7" 
                                            @click="handlePackageRenewal(pkg)"
                                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                        <i class="fas fa-clock mr-2"></i>
                                        Gia hạn
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Available Packages -->
                    <div class="available-packages">
                        <h4 class="text-lg font-semibold text-white mb-3">Đăng ký thêm gói</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <div v-for="pkg in availablePackages" :key="pkg.id" 
                                 class="package-option p-4 bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors"
                                 @click="handlePackageSwitch(pkg)">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-white font-semibold">{{ pkg.name }}</div>
                                        <div class="text-gray-400 text-sm">{{ pkg.description }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-400 font-bold">{{ formatPrice(pkg.price) }}đ</div>
                                        <div class="text-sm text-gray-400">/ tháng</div>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span v-for="(feature, index) in pkg.features.slice(0, 3)" :key="index"
                                          class="px-2 py-1 bg-gray-700 rounded-full text-xs text-gray-300">
                                        {{ feature }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';

export default {
    setup() {
        const packages = ref([]);
        const loading = ref(true);
        const membership = ref({
            active: false,
            package: null,
            days_left: 0,
            end_at: null,
            start_at: null
        });
        const membershipLoading = ref(true);

        const showPaymentModal = ref(false);
        const selectedPackage = ref(null);
        const selectedDuration = ref(null);
        const selectedPaymentMethod = ref(null);

        const showManageModal = ref(false);
        const activePackages = ref([]);
        const availablePackages = ref([]);

        const durations = [
            { label: '1 tháng', value: 1, save: 0 },
            { label: '3 tháng', value: 3, save: 10 },
            { label: '6 tháng', value: 6, save: 15 },
            { label: '1 năm', value: 12, save: 20 }
        ];

        const paymentMethods = [
            { id: 'vnpay', name: 'VNPay', icon: '/images/payment/vnpay.png' },
            { id: 'momo', name: 'MoMo', icon: '/images/payment/momo.png' },
            { id: 'zalopay', name: 'ZaloPay', icon: '/images/payment/zalopay.png' },
            { id: 'bank', name: 'Chuyển khoản ngân hàng', icon: '/images/payment/bank.png', disabled: true, note: 'Đang phát triển' }
        ];

        const calculatePrice = (duration) => {
            if (!selectedPackage.value) return 0;
            const basePrice = selectedPackage.value.price;
            const save = durations.find(d => d.value === duration)?.save || 0;
            return basePrice * duration * (1 - save / 100);
        };

        const totalPrice = computed(() => {
            return calculatePrice(selectedDuration.value || 1);
        });

        const selectDuration = (duration) => {
            selectedDuration.value = duration;
        };

        const selectPaymentMethod = (methodId) => {
            selectedPaymentMethod.value = methodId;
        };

        const fetchPackages = async () => {
            try {
                const response = await axios.get('/api/membership-packages');
                packages.value = response.data.map((pkg, idx) => ({
                    ...pkg,
                    image: pkg.image || sampleImages[idx % sampleImages.length]
                }));
                loading.value = false;
            } catch (error) {
                toast.error('Không thể tải danh sách gói');
                loading.value = false;
            }
        };

        const fetchMembership = async () => {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    membership.value = {
                        active: false,
                        package: null,
                        days_left: 0,
                        end_at: null,
                        start_at: null
                    };
                    membershipLoading.value = false;
                    return;
                }

                const res = await axios.get('/api/user/membership', { 
                    headers: { Authorization: `Bearer ${token}` } 
                });

                if (res.data) {
                    // Lấy tất cả gói đang active
                    activePackages.value = Array.isArray(res.data.active_packages) ? res.data.active_packages : [];
                    
                    // Lấy gói cao cấp nhất làm gói hiện tại
                    const highestPackage = activePackages.value.reduce((highest, current) => {
                        return (!highest || current.id > highest.id) ? current : highest;
                    }, null);

                    // Tính số ngày còn lại cho gói cao cấp nhất
                    let daysLeft = 0;
                    if (highestPackage && highestPackage.end_at) {
                        const endDate = new Date(highestPackage.end_at);
                        const now = new Date();
                        daysLeft = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));
                    }

                    // Cập nhật thông tin membership
                    membership.value = {
                        active: res.data.active || false,
                        package: highestPackage || null,
                        days_left: daysLeft,
                        end_at: highestPackage ? highestPackage.end_at : null,
                        start_at: highestPackage ? highestPackage.start_at : null
                    };
                    
                    // Lấy các gói có thể đăng ký thêm
                    availablePackages.value = packages.value.filter(pkg => 
                        !activePackages.value.some(active => active.id === pkg.id)
                    );
                }

            } catch (e) {
                membership.value = {
                    active: false,
                    package: null,
                    days_left: 0,
                    end_at: null,
                    start_at: null
                };
                activePackages.value = [];
                availablePackages.value = [];
            } finally {
                membershipLoading.value = false;
            }
        };

        // Ảnh minh họa mẫu (có thể thay bằng ảnh thực tế)
        const sampleImages = [
            'https://img.vieon.vn/mb/2023/03/16/1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b_640_360.jpg',
            'https://img.vieon.vn/mb/2023/03/16/2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b2b_640_360.jpg',
            'https://img.vieon.vn/mb/2023/03/16/3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b_640_360.jpg',
            'https://img.vieon.vn/mb/2023/03/16/4b4b4b4b4b4b4b4b4b4b4b4b4b4b4b4b_640_360.jpg',
        ];

        const isPackageOwned = (packageId) => {
            // Kiểm tra trong activePackages
            const isActive = activePackages.value.some(pkg => pkg.id === packageId);
            
            // Kiểm tra trong membership.package
            const isCurrentPackage = membership.value.package?.id === packageId;
            
            return isActive || isCurrentPackage;
        };

        const formatPrice = (price) => {
            return new Intl.NumberFormat('vi-VN', {
                style: 'decimal',
                maximumFractionDigits: 0
            }).format(price);
        };

        const cardClass = (idx) => {
            const arr = ['border-green-dark', 'border-blue-dark', 'border-red-dark', 'border-yellow-dark'];
            return arr[idx % arr.length];
        };

        const headerClass = (idx) => {
            const arr = ['header-green-dark', 'header-blue-dark', 'header-red-dark', 'header-yellow-dark'];
            return arr[idx % arr.length];
        };

        const btnClass = (idx) => {
            const arr = ['btn-green-dark', 'btn-blue-dark', 'btn-red-dark', 'btn-yellow-dark'];
            return arr[idx % arr.length];
        };

        const isPackageExpiring = (packageId) => {
            const pkg = activePackages.value.find(p => p.id === packageId);
            if (!pkg) return false;
            
            // Nếu còn dưới 7 ngày thì hiển thị gia hạn
            return pkg.days_left <= 7;
        };

        const handleSubscribe = async (packageId) => {
            const selected = packages.value.find(pkg => pkg.id === packageId);
            if (!selected) {
                toast.error('Không tìm thấy gói!');
                return;
            }

            // Kiểm tra nếu gói đã tồn tại và sắp hết hạn
            const existingPackage = activePackages.value.find(pkg => pkg.id === packageId);
            if (existingPackage && existingPackage.days_left <= 7) {
                selectedPackage.value = selected;
                selectedDuration.value = 1;
                showPaymentModal.value = true;
                return;
            }

            // Nếu gói đã tồn tại và chưa sắp hết hạn
            if (existingPackage) {
                toast.info('Bạn đã sở hữu gói này!');
                return;
            }

            selectedPackage.value = selected;
            selectedDuration.value = 1;
            showPaymentModal.value = true;
        };

        const closePaymentModal = () => {
            showPaymentModal.value = false;
            selectedPackage.value = null;
            selectedDuration.value = null;
            selectedPaymentMethod.value = null;
        };

        const closeManageModal = () => {
            showManageModal.value = false;
        };

        const handlePackageSwitch = async (pkg) => {
            if (membership.value.package?.id === pkg.id) {
                toast.info('Bạn đang sử dụng gói này!');
                return;
            }

            selectedPackage.value = pkg;
            selectedDuration.value = 1;
            showManageModal.value = false;
            showPaymentModal.value = true;
        };

        const processPayment = async () => {
            if (!selectedPaymentMethod.value || !selectedDuration.value || !selectedPackage.value) {
                toast.error('Vui lòng chọn đầy đủ thông tin thanh toán!');
                return;
            }

            // Kiểm tra nếu phương thức thanh toán đang phát triển
            const selectedMethod = paymentMethods.find(m => m.id === selectedPaymentMethod.value);
            if (selectedMethod?.disabled) {
                toast.info('Phương thức thanh toán này đang được phát triển. Vui lòng chọn phương thức khác!');
                return;
            }

            try {
                const response = await axios.post('/api/payment/create', {
                    package_id: selectedPackage.value.id,
                    duration: selectedDuration.value,
                    payment_method: selectedPaymentMethod.value,
                    amount: calculatePrice(selectedDuration.value),
                    current_package_id: membership.value.active ? membership.value.package.id : null,
                    is_upgrade: membership.value.active && 
                               packages.value.findIndex(p => p.id === selectedPackage.value.id) > 
                               packages.value.findIndex(p => p.id === membership.value.package.id)
                }, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (response.data.url) {
                    // Chuyển hướng trực tiếp đến trang thanh toán
                    window.location.href = response.data.url;
                } else {
                    toast.error('Không lấy được link thanh toán!');
                }
            } catch (error) {
                console.error('Payment error:', error);
                if (error.response?.status === 401) {
                    toast.error('Vui lòng đăng nhập để tiếp tục!');
                } else {
                    toast.error(error.response?.data?.message || 'Lỗi khi tạo giao dịch thanh toán!');
                }
            }
        };

        const handlePackageRenewal = (pkg) => {
            selectedPackage.value = pkg;
            selectedDuration.value = 1;
            showManageModal.value = false;
            showPaymentModal.value = true;
        };

        onMounted(() => {
            fetchPackages();
            fetchMembership();
        });

        return {
            packages,
            loading,
            membership,
            membershipLoading,
            showPaymentModal,
            selectedPackage,
            selectedDuration,
            selectedPaymentMethod,
            showManageModal,
            activePackages,
            availablePackages,
            durations,
            paymentMethods,
            isPackageOwned,
            formatPrice,
            cardClass,
            headerClass,
            btnClass,
            handleSubscribe,
            closePaymentModal,
            closeManageModal,
            handlePackageSwitch,
            processPayment,
            calculatePrice,
            isPackageExpiring,
            handlePackageRenewal,
            totalPrice,
            selectDuration,
            selectPaymentMethod
        };
    }
};
</script>

<style scoped>
.membership-dark-v2 {
    min-height: 100vh;
    color: #fff;
}
.vip-title-dark {
    font-size: 2.2rem;
    font-weight: bold;
    color: #fff;
}
.vip-features-row {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    align-items: center;
    margin-bottom: 1.5rem;
    justify-content: center;
}
.vip-feature-dark {
    background: #23262f;
    border-radius: 20px;
    padding: 6px 18px;
    font-size: 1rem;
    color: #a3e635;
    display: flex;
    align-items: center;
    font-weight: 500;
    white-space: nowrap;
}
.btn-back-dark {
    background: none;
    border: none;
    color: #a3e635;
    font-size: 1.1rem;
    font-weight: 500;
    cursor: pointer;
}
.btn-back-dark i {
    margin-right: 6px;
}
.membership-cards-dark {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 32px;
}
.membership-card-dark-v2 {
    background: #23262f;
    border-radius: 20px;
    box-shadow: 0 4px 24px 0 rgba(0,0,0,0.18);
    border: 2.5px solid #23262f;
    width: 340px;
    min-width: 280px;
    max-width: 360px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    will-change: transform, box-shadow;
}
.membership-card-dark-v2:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 40px 0 rgba(0,0,0,0.3);
}
.border-green-dark { 
    border-color: #4ade80 !important; 
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.border-blue-dark { 
    border-color: #60a5fa !important; 
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.border-red-dark { 
    border-color: #f87171 !important; 
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.border-yellow-dark { 
    border-color: #fde047 !important; 
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.membership-card-dark-v2:hover.border-green-dark {
    border-color: #4ade80 !important;
    box-shadow: 0 0 25px rgba(74, 222, 128, 0.4);
}
.membership-card-dark-v2:hover.border-blue-dark {
    border-color: #60a5fa !important;
    box-shadow: 0 0 25px rgba(96, 165, 250, 0.4);
}
.membership-card-dark-v2:hover.border-red-dark {
    border-color: #f87171 !important;
    box-shadow: 0 0 25px rgba(248, 113, 113, 0.4);
}
.membership-card-dark-v2:hover.border-yellow-dark {
    border-color: #fde047 !important;
    box-shadow: 0 0 25px rgba(253, 224, 71, 0.4);
}
.card-header-dark-v2 {
    padding: 24px 20px 16px 20px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    position: relative;
    min-height: 120px;
}
.header-green-dark {
    background: linear-gradient(90deg, #23262f 0%, #4ade80 100%);
}
.header-blue-dark {
    background: linear-gradient(90deg, #23262f 0%, #60a5fa 100%);
}
.header-red-dark {
    background: linear-gradient(90deg, #23262f 0%, #f87171 100%);
}
.header-yellow-dark {
    background: linear-gradient(90deg, #23262f 0%, #fde047 100%);
}
.header-content-dark {
    flex: 1;
}
.pkg-title-dark {
    font-size: 1.2rem;
    font-weight: bold;
    color: #fff;
    margin-bottom: 8px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.pkg-price-dark {
    font-size: 2.1rem;
    font-weight: bold;
    color: #a3e635;
    margin-bottom: 4px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.currency-dark {
    font-size: 1.1rem;
    color: #a3e635;
    margin-left: 2px;
}
.pkg-desc-dark {
    color: #e5e7eb;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    text-shadow: 0 1px 4px rgba(0,0,0,0.10);
}
.pkg-img-dark {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.18);
    margin-left: 12px;
    transform: rotate(-8deg);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.membership-card-dark-v2:hover .pkg-img-dark {
    transform: rotate(-5deg) scale(1.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
}
.card-body-dark-v2 {
    padding: 20px 20px 24px 20px;
    background: #23262f;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.feature-list-dark-v2 {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
}
.feature-list-dark-v2 li {
    display: flex;
    align-items: center;
    font-size: 1rem;
    color: #fff;
    margin-bottom: 0.5rem;
}
.btn-dark-cta-v2 {
    border: none;
    border-radius: 24px;
    font-size: 1.1rem;
    font-weight: bold;
    padding: 12px 0;
    margin-top: auto;
    width: 100%;
    transition: background 0.2s, color 0.2s;
}
.btn-green-dark {
    background: linear-gradient(90deg, #4ade80 0%, #a3e635 100%);
    color: #181a20;
}
.btn-green-dark:hover {
    background: linear-gradient(90deg, #a3e635 0%, #4ade80 100%);
    color: #23262f;
}
.btn-blue-dark {
    background: linear-gradient(90deg, #60a5fa 0%, #3b82f6 100%);
    color: #181a20;
}
.btn-blue-dark:hover {
    background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
    color: #23262f;
}
.btn-red-dark {
    background: linear-gradient(90deg, #f87171 0%, #ef4444 100%);
    color: #181a20;
}
.btn-red-dark:hover {
    background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
    color: #23262f;
}
.btn-yellow-dark {
    background: linear-gradient(90deg, #fde047 0%, #eab308 100%);
    color: #181a20;
}
.btn-yellow-dark:hover {
    background: linear-gradient(90deg, #eab308 0%, #fde047 100%);
    color: #23262f;
}
/* Hiệu ứng sáng viền khi hover card */
@media (max-width: 991px) {
    .membership-cards-dark {
        gap: 20px;
    }
    .membership-card-dark-v2 {
        width: 45%;
        min-width: 220px;
        max-width: 100%;
    }
    .pt-header {
        padding-top: 1.5rem;
    }
    .pr-vip-features {
        padding-right: 1rem;
    }
    .vip-features-row {
        justify-content: center;
    }
}
@media (max-width: 767px) {
    .membership-cards-dark {
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }
    .membership-card-dark-v2 {
        width: 100%;
        min-width: 0;
        max-width: 100%;
    }
    .card-header-dark-v2, .card-body-dark-v2 { padding: 14px; }
    .pkg-price-dark { font-size: 1.5rem; }
}
@media (max-width: 600px) {
    .vip-features-row {
        gap: 10px;
        justify-content: center;
    }
    .vip-feature-dark {
        font-size: 0.95rem;
        padding: 5px 12px;
    }
}
.pt-header {
    padding-top: 2.5rem;
}
.pr-vip-features {
    padding-right: 2.5rem;
}
.membership-flex-outer {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-left: 2.5rem;
    padding-right: 2.5rem;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    margin-top: 64px;
}
.membership-header-flex {
    width: 100%;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
}
@media (max-width: 991px) {
    .membership-flex-outer {
        padding-left: 1rem;
        padding-right: 1rem;
        margin-top: 56px;
    }
    .pt-header {
        padding-top: 1.5rem;
    }
}
@media (max-width: 767px) {
    .membership-header-flex {
        align-items: flex-start;
    }
    .membership-flex-outer {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        margin-top: 48px;
    }
}

.payment-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(4px);
}

.payment-modal {
    background: #23262f;
    border-radius: 20px;
    width: 95%;
    max-width: 650px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

.payment-modal::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Thêm smooth scroll cho toàn bộ trang */
html {
    scroll-behavior: smooth;
}

/* Thêm smooth scroll cho modal */
.payment-modal {
    scroll-behavior: smooth;
}

.payment-modal-header {
    padding: 24px 28px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.close-modal {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 5px;
}

.payment-modal-body {
    padding: 28px;
}

.duration-options {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.duration-option {
    background: #2a2d36;
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 20px 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.duration-option.selected {
    border-color: #4ade80;
    background: rgba(74, 222, 128, 0.1);
}

.duration-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 8px;
}

.duration-price {
    font-size: 1.2rem;
    font-weight: bold;
    color: #4ade80;
    margin-bottom: 4px;
}

.duration-save {
    font-size: 0.8rem;
    color: #4ade80;
    background: rgba(74, 222, 128, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-block;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-top: 24px;
}

.payment-method {
    background: #2a2d36;
    border: 2px solid transparent;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method.selected {
    border-color: #4ade80;
    background: rgba(74, 222, 128, 0.1);
}

.payment-method.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    position: relative;
}

.payment-icon {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.payment-name {
    color: #fff;
    font-weight: 500;
}

.payment-note {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ef4444;
    color: white;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    white-space: nowrap;
}

.payment-modal-footer {
    padding: 24px 28px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.total-price {
    display: flex;
    flex-direction: column;
}

.btn-pay-now {
    background: linear-gradient(90deg, #4ade80 0%, #a3e635 100%);
    color: #181a20;
    border: none;
    border-radius: 12px;
    padding: 14px 32px;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-pay-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
}

.btn-pay-now:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 768px) {
    .payment-modal {
        width: 90%;
        max-width: 500px;
    }
    
    .payment-modal-header,
    .payment-modal-body,
    .payment-modal-footer {
        padding: 20px;
    }
    
    .duration-options {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .payment-modal-footer {
        flex-direction: column;
        gap: 16px;
    }
    
    .btn-pay-now {
        width: 100%;
        padding: 12px 24px;
    }
}

@media (max-width: 480px) {
    .payment-modal {
        width: 95%;
        margin: 10px;
    }
    
    .duration-options {
        grid-template-columns: 1fr;
    }
}

.package-option {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.package-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.package-option.border-green-500 {
    background: rgba(74, 222, 128, 0.1);
}
</style> 