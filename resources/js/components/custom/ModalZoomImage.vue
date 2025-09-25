<script setup lang="ts">
import { ref, watch } from 'vue'

const {isZoom} = defineProps<{isZoom: boolean}>()
const modalImageRef = ref<HTMLDialogElement>()

watch(() => isZoom, () => modalImageRef.value?.show())

function closeModal(){
    emit('close-modal')
}

const emit = defineEmits<{
    (e: 'close-modal'):void
}>()
</script>

<template>
    <dialog id="zoom_image" ref="modalImageRef" class="modal">
        <div class="modal-box w-8/12 max-w-5xl">
            <slot></slot>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button @click="closeModal">close</button>
        </form>
    </dialog>
</template>

<style lang="scss" scoped></style>