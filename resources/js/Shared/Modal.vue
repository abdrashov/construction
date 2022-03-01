<template>
    <transition leave-active-class="duration-200">
        <div v-show="isOpen" class="fixed bottom-0 inset-0 flex items-start justify-center overflow-y-auto">
            <transition enter-active-class="duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <!--
    Background overlay, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
        -->
                <div v-show="isOpen" class="fixed inset-0 transition-opacity">
                    <div class="absolute inset-0 bg-black opacity-20" @click="$emit('close')"></div>
                </div>
            </transition>
            <!--
    Modal panel, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      To: "opacity-100 translate-y-0 sm:scale-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100 translate-y-0 sm:scale-100"
      To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
            <transition
                enter-active-class="duration-300 ease-out"
                enter-from-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                enter-to-class="translate-y-0 opacity-100 sm:scale-100"
                leave-active-class="duration-200 ease-in"
                leave-from-class="translate-y-0 opacity-100 sm:scale-100"
                leave-to-class="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
            >
                <div v-show="isOpen" class="my-16 p-6 w-full bg-white rounded-lg shadow-lg transform transition-all sm:max-w-xl" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <slot></slot>
                </div>
            </transition>
        </div>
    </transition>
</template>
<script>
export default {
    props: {
        isOpen: {
            type: Boolean,
            default: false,
            required: true,
        },
    },
}
</script>
