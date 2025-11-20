<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { Check, HandCoins, NotebookText, SearchIcon } from 'lucide-vue-next';
import Alert from '@/components/ui/alert/Alert.vue';
import AlertDescription from '@/components/ui/alert/AlertDescription.vue';
import { TransactionResource } from '@/types/transaction';
import { Links, Meta } from '@/types/pagination';
import { formatCurrency } from '@/utils/helper';
import { useDebounce } from '@vueuse/core';
import CustomPaginate from '@/components/custom/CustomPaginate.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
];

interface FilterType {
    keyword: string,
    category_id: number | null,
    product_id: number | null,
    sort_by: string | null,
    sort_type: string | null,
    date: string | null,
}

interface Response {
    data: TransactionResource[],
    links: Links,
    meta: Meta,
}

const { transactions, search } = defineProps<{
    search: FilterType | null,
    transactions: Response,
}>()

const filters = reactive<FilterType>({
    keyword: search?.keyword || "",
    category_id: search?.category_id || null,
    product_id: search?.product_id || null,
    sort_by: '',
    sort_type: '',
    date: '',
})

const sortKey = ref<keyof TransactionResource | null>(null);
const sortAsc = ref(true);

function sortTable(key: keyof TransactionResource) {
    sortAsc.value = !sortAsc.value
    sortKey.value = key
    filters.sort_by = key
    if (sortAsc.value) {
        filters.sort_type = 'asc'
    } else {
        filters.sort_type = 'desc'
    }
}

function numberList(i: number) {
    if (transactions.data.length > 0) {
        const currentPage = transactions.meta.current_page;
        const perPage = transactions.meta.per_page;

        return i + ((currentPage - 1) * perPage)
    }
}

const pageData = usePage()
const showMessage = ref<boolean>(false);
const message = ref<string>('');
const flash = (pageData.props as { flash?: { message?: string } }).flash;
if (flash && flash.message) {
    showMessage.value = true;
    message.value = flash.message;
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
}

const debounce = useDebounce(computed(() => ({ ...filters })), 400)
watch(debounce,
    () => {
        router.get(route('transactions.index'), { filters: filters }, { preserveState: true, preserveScroll: true, replace: true })
    },
    { deep: true }
)
</script>

<template>

    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2 pb-8">
            <div class="grid grid-cols-1 grid-rows-2 md:grid-cols-3 md:grid-rows-1 mx-6 mt-2 mb-6">
                <div>
                    <Alert class="bg-green-50 max-w-250 border-green-200 text-green-700" v-if="showMessage">
                        <Check class="h-4 w-4" />
                        <!-- <AlertTitle>Success</AlertTitle> -->
                        <AlertDescription>
                            {{ message }}
                        </AlertDescription>
                    </Alert>
                </div>
                <div class="flex justify-end col-span-2">
                    <input v-model="filters.date" type="date"
                        class="input input-accent border-2 border-green-200 mr-4" />
                    <label class="input input-md input-accent border-2 border-green-200 mr-4">
                        <SearchIcon class="text-green-300 text-sm"></SearchIcon>
                        <input v-model="filters.keyword" type="search" required placeholder="Search" />
                    </label>
                    <Link :href="route('transactions.create')">
                    <Button
                        class="bg-teal-400 hover:shadow-lg hover:shadow-teal-100 hover:bg-teal-500 cursor-pointer">Transaksi
                        Baru
                        <HandCoins />
                    </Button>
                    </Link>
                </div>
                <!-- name of each tab group should be unique -->
            </div>
            <template v-if="transactions.data.length > 0">
                <div class="overflow-x-auto mx-6">
                    <table class="table table-zebra bg-gray-50">
                        <thead class="bg-teal-200">
                            <tr>
                                <th>No</th>
                                <th @click="sortTable('invoice_code')" class="cursor-pointer">
                                    Kode Invoice
                                    <span v-if="sortKey === 'invoice_code'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th @click="sortTable('buyer_name')" class="cursor-pointer">
                                    Pembeli
                                    <span v-if="sortKey === 'buyer_name'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th @click="sortTable('transaction_date')" class="cursor-pointer">
                                    Tanggal
                                    <span v-if="sortKey === 'transaction_date'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th @click="sortTable('is_installment')" class="cursor-pointer">
                                    Tipe
                                    <span v-if="sortKey === 'is_installment'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th @click="sortTable('is_done')" class="cursor-pointer">
                                    Status
                                    <span v-if="sortKey === 'is_done'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th @click="sortTable('grand_total')" class="cursor-pointer">
                                    Grand Total
                                    <span v-if="sortKey === 'grand_total'" class="ml-2">
                                        {{ sortAsc ? "▲" : "▼" }}
                                    </span>
                                </th>
                                <th class="w-1/12">Pembelian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(transaction, i) in transactions.data" :key="transaction.id">
                                <tr>
                                    <td>{{ numberList(i + 1) }}</td>
                                    <td class="font-bold text-gray-500">{{ transaction.invoice_code }}</td>
                                    <td>
                                        <div class="font-bold text-md uppercase">
                                            {{ transaction.buyer_name }}
                                        </div>
                                    </td>
                                    <td>{{ transaction.transaction_date }}</td>
                                    <td>
                                        <div class="text-sm">
                                            <template v-if="transaction.is_installment">
                                                <div class="badge badge-soft badge-secondary">Cicilan</div>
                                            </template>
                                            <template v-else>
                                                <span class="text-green-400">
                                                    <div class="badge badge-soft badge-primary">Tunai</div>
                                                </span>
                                            </template>
                                        </div>
                                    </td>
                                    <td>
                                        <div v-if="transaction.is_done" class="badge badge-success text-white px-3">
                                            <svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24">
                                                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                                                    <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                                        stroke-linecap="square" stroke-miterlimit="10" stroke-width="2">
                                                    </circle>
                                                    <polyline points="7 13 10 16 17 8" fill="none" stroke="currentColor"
                                                        stroke-linecap="square" stroke-miterlimit="10" stroke-width="2">
                                                    </polyline>
                                                </g>
                                            </svg>
                                            Selesai
                                        </div>
                                        <div v-else class="badge badge-info text-white px-3">
                                            <svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24">
                                                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                                                    <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                                        stroke-linecap="square" stroke-miterlimit="10" stroke-width="2">
                                                    </circle>
                                                    <path d="m12,17v-5.5c0-.276-.224-.5-.5-.5h-1.5" fill="none"
                                                        stroke="currentColor" stroke-linecap="square"
                                                        stroke-miterlimit="10" stroke-width="2"></path>
                                                    <circle cx="12" cy="7.25" r="1.25" fill="currentColor"
                                                        stroke-width="2"></circle>
                                                </g>
                                            </svg>
                                            Proses
                                        </div>
                                    </td>
                                    <td class="font-bold text-red-500">{{ formatCurrency(transaction.grand_total) }}
                                    </td>
                                    <td>
                                        <!-- <template v-for="(v,i) in transaction.transaction_details" :key="v.id">
                                            <div v-if="i < 2" class="text-teal-500 hover:text-teal-600 hover:underline mb-1">
                                                <Link :href="route('variants.show', v.variant_id)">
                                                    {{ v.product_history?.name }} {{ v.product_history?.merk }} ({{ v.quantity }} {{ v.product_history?.unit }})
                                                </Link>
                                            </div>
                                        </template> -->
                                        <div v-if="transaction.transaction_details.length > 0">
                                            <div class="text-gray-400 font-bold text-md text-teal-500">
                                                {{ transaction.transaction_details.length }} Barang
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <Link :href="route('transactions.show', transaction.id ?? 0)"
                                            class="relative inline-block group">
                                        <button class="text-teal-500 hover:text-teal-600 cursor-pointer">
                                            <NotebookText></NotebookText>
                                        </button>
                                        <div
                                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                            Lihat Detail
                                        </div>
                                        </Link>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <CustomPaginate :pagination="transactions.meta"></CustomPaginate>
                </div>
            </template>
        </div>
    </AppLayout>
</template>