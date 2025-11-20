<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { formatCurrency, terbilang } from '../../utils/helper';
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronsLeft } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import { InstallmentPaymentResource, TransactionResource } from '@/types/transaction';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
];

const paymentModalRef = ref<HTMLDialogElement | null>(null);
const { transaction } = defineProps<{
    transaction: TransactionResource,
}>();
// const nominalPayment = ref<number>(0);

const getNow = function () {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
const form = useForm<{ transaction_id: number | null, nominal_payment: number, is_done: boolean, payment_date: string }>({
    transaction_id: transaction.id,
    nominal_payment: 0,
    is_done: false,
    payment_date: getNow()
})
const cashBack = computed(() => {
    if (form.nominal_payment > remainingAmount.value) {
        return form.nominal_payment - remainingAmount.value;
    }
    return 0;
});

const remainingAmount = computed(() => {
    if (transaction.is_installment) {
        const totalPaid = transaction.installment_payments.reduce((sum: number, item: InstallmentPaymentResource) => sum + Number(item.amount), 0);
        return transaction.grand_total - totalPaid;
    }
    return 0;
});

function handlePayment() {
    // Logic to handle the payment submission
    console.log('Payment submitted');
    if (form.nominal_payment >= remainingAmount.value) {
        form.nominal_payment = form.nominal_payment - cashBack.value
        form.is_done = true
    }

    form.post(route('installment.payment'), {
        onError(e) {
            console.log(e);
        }
    });
    paymentModalRef.value?.close();
}

function closeModal() {
    paymentModalRef.value?.close();
}
</script>

<template>

    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaction Detail
            </h2>
        </template>

        <div class="py-5">
            <Link :href="route('transactions.index')"
                class="inline-block text-gray-500 hover:text-gray-700 sm:px-6 lg:px-8 mb-4">
            <ChevronsLeft class="inline size-[1.2em]" />
            Kembali ke Daftar Transaksi
            </Link>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <table class="table-auto w-full">
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2 font-bold w-1/3">Kode Invoice</td>
                                <td class="border px-4 py-2 font-bold text-gray-600">{{ transaction.invoice_code }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-bold">Buyer Name</td>
                                <td class="border px-4 py-2 capitalize">{{ transaction.buyer_name }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-bold">Jenis Pembayaran</td>
                                <td class="border px-4 py-2">
                                    <div class="text-sm">
                                        <template v-if="transaction.is_installment">
                                            <div class="badge badge-soft badge-secondary px-3">Cicilan</div>
                                        </template>
                                        <template v-else>
                                            <span class="text-green-400">
                                                <div class="badge badge-soft badge-primary px-3">Tunai</div>
                                            </span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-bold">Tanggal Transaksi</td>
                                <td class="border px-4 py-2">{{ transaction.transaction_date }}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-bold">Status</td>
                                <td class="border px-4 py-2">
                                    <div v-if="transaction.is_done" class="badge badge-success text-white px-3">
                                        <svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
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
                                        <svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                                                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor"
                                                    stroke-linecap="square" stroke-miterlimit="10" stroke-width="2">
                                                </circle>
                                                <path d="m12,17v-5.5c0-.276-.224-.5-.5-.5h-1.5" fill="none"
                                                    stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10"
                                                    stroke-width="2"></path>
                                                <circle cx="12" cy="7.25" r="1.25" fill="currentColor" stroke-width="2">
                                                </circle>
                                            </g>
                                        </svg>
                                        Proses
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2 font-bold">Grand Total</td>
                                <td class="border px-4 py-2 font-bold text-red-500">{{
                                    formatCurrency(transaction.grand_total)
                                    }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div>
                        <h3 class="font-semibold text-lg text-gray-700 mt-8 mb-3">Detail Barang</h3>
                    </div>
                    <table class="table-auto w-full">
                        <thead class="bg-teal-200">
                            <tr>
                                <th>#</th>
                                <th class="border px-4 py-2">Nama Barang</th>
                                <th class="border px-4 py-2">Varian</th>
                                <th class="border px-4 py-2">Warna</th>
                                <th class="border px-4 py-2">Kunatitas</th>
                                <th class="border px-4 py-2">Harga</th>
                                <th class="border px-4 py-2">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(detail, i) in transaction.transaction_details" :key="detail.id">
                                <tr class="hover:bg-gray-100 text-center capitalize">
                                    <td class="border px-4 py-2">{{ i + 1 }}</td>
                                    <td class="border px-4 py-2">{{ detail.product_history?.name }}</td>
                                    <td class="border px-4 py-2">{{ detail.product_history?.merk }}</td>
                                    <td class="border px-4 py-2">{{ detail.product_history?.color }}</td>
                                    <td class="border px-4 py-2">{{ detail.quantity }}</td>
                                    <td class="border px-4 py-2 font-bold text-red-500">{{ formatCurrency(detail.price)
                                        }}</td>
                                    <td class="border px-4 py-2 font-bold text-red-500">{{
                                        formatCurrency(detail.sub_total) }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div v-if="transaction.is_installment">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-700 mt-8 mb-3" v-if="transaction.is_installment">
                                Detail
                                Pembayaran Cicilan</h3>
                        </div>
                        <table class="table-auto w-full">
                            <thead class="bg-teal-200">
                                <tr>
                                    <th class="border px-4 py-2 w-1/5">Pembayaran Ke</th>
                                    <th class="border px-4 py-2">Tanggal Pembayaran</th>
                                    <th class="border px-4 py-2">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(payment, j) in transaction.installment_payments" :key="payment.id">
                                    <tr class="hover:bg-gray-100 text-center">
                                        <td class="border px-4 py-2">{{ j + 1 }}</td>
                                        <td class="border px-4 py-2">{{ payment.payment_date }}</td>
                                        <td class="border px-4 py-2 font-bold text-red-500">{{
                                            formatCurrency(payment.amount) }}</td>
                                    </tr>
                                </template>
                                <template v-if="transaction.installment_payments.length === 0">
                                    <tr>
                                        <td class="border px-4 py-2 text-center" colspan="3">Tidak ada data pembayaran
                                            cicilan.</td>
                                    </tr>
                                </template>
                                <template v-if="remainingAmount > 0">
                                    <tr>
                                        <td class="border px-4 py-2 font-bold text-right" colspan="2">Sisa Cicilan:
                                        </td>
                                        <td class="border px-4 py-2 font-bold text-red-500 text-center">{{
                                            formatCurrency(remainingAmount) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="border px-4 py-2 font-bold text-right">
                                            <button onclick="my_modal_4.showModal()"
                                                class="btn btn-primary btn-sm btn-block">Bayar Sisa Cicilan</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- You can open the modal using ID.showModal() method -->
            <dialog ref="paymentModalRef" id="my_modal_4" class="modal">
                <div class="modal-box w-11/12 max-w-3xl">
                    <form method="dialog" @submit.prevent="handlePayment">
                        <h3 class="text-lg font-bold">Pembayaran Cicilan</h3>
                        <table class="table-auto w-full my-4">
                            <tbody>
                                <tr>
                                    <td class="border px-4 py-2 font-bold w-1/3">Kode Invoice</td>
                                    <td class="border px-4 py-2 font-bold text-gray-600">{{ transaction.invoice_code }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-bold">Buyer Name</td>
                                    <td class="border px-4 py-2 capitalize">{{ transaction.buyer_name }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-bold">Grand Total</td>
                                    <td class="border px-4 py-2 font-bold text-red-500">{{
                                        formatCurrency(transaction.grand_total)
                                        }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-bold">Sisa Cicilan</td>
                                    <td class="border px-4 py-2 font-bold text-red-500">
                                        {{ formatCurrency(remainingAmount) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-bold">Nominal Pembayaran</td>
                                    <td class="border px-4 py-2 font-bold text-gray-500">
                                        <input type="number" v-model="form.nominal_payment"
                                            placeholder="Masukkan nominal pembayaran"
                                            class="input input-bordered w-1/3 max-w-xs mr-2" />
                                        <span class="text-sm w-2/3 italic text-green-600">{{
                                            terbilang(form.nominal_payment)
                                            }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-bold">Kembalian</td>
                                    <td class="border px-4 py-2 font-bold text-green-600">
                                        {{ formatCurrency(cashBack) }}
                                    </td>
                                </tr>
                                <!-- <tr>
                                <td></td>
                                <td class="border px-4 py-2 text-sm italic text-green-600">{{ terbilang(nominalPayment) }}</td>
                            </tr> -->
                            </tbody>
                        </table>
                        <div class="modal-action">
                            <!-- <form method="dialog"> -->
                            <!-- if there is a button, it will close the modal -->
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button @click.prevent="closeModal" class="btn">Kembali</button>
                            <!-- </form> -->
                        </div>
                    </form>
                </div>
            </dialog>
        </div>
    </AppLayout>
</template>

<style lang="css" scoped></style>