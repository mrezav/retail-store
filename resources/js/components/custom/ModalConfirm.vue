<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { watch, ref } from 'vue';

const { modalData } = defineProps<{
    modalData: {
        showModal: boolean,
        targetUrl: string,
    }
}>()

const modalRef = ref<HTMLDialogElement | null>()
watch(() => modalData.showModal, () => {
    modalRef.value?.show()
})

const emit = defineEmits<{
    (e: 'closeModal'): void
}>()

function handleClose() {
    console.log('close modal')
    modalRef.value?.close()
    emit("closeModal")
}

function handleDelete(){
    router.visit(modalData.targetUrl, {
        method:'delete',
        preserveScroll: false
    })
    console.log('delete')
    modalRef.value?.close()
    emit("closeModal")
}


</script>

<template>
    <Teleport to="body">
        <dialog ref="modalRef" class="modal">
            <!-- <div class="fixed overflow-x-auto overflow-y-auto inset-0 flex justify-center items-center z-20" v-if="modalData.showModal"> -->
            <!-- <div class="relative mx-auto width-auto shadow-xl-20 max-w-2xl"> -->
            <div class="modal-box">
                <slot></slot>
                <form method="dialog" class="modal-backdrop">
                    <button @click="handleClose" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                    <div class="modal-action">
                        <Link @click.prevent="handleDelete">
                            <button
                            class="btn btn-warning text-white">Yes</button>
                        </Link>
                        <button @click="handleClose" class="btn">No</button>
                        <!-- <div @click="handleClose" class="absolute top-0 right-0 m-4 cursor-pointer text-gray text-2xl font-bold">x</div> -->
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button @click="handleClose">close</button>
            </form>
            <!-- </div> -->
            <!-- </div> -->
        </dialog>
        <!-- <div v-if="modalData.showModal" class="fixed inset-0 z-10 opacity-60 bg-black "></div> -->
    </Teleport>
</template>

<style lang="scss" scoped></style>