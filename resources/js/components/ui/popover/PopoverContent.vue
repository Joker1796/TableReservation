<script setup lang="ts">
import type { PopoverContentEmits, PopoverContentProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { PopoverContent, PopoverPortal, useForwardPropsEmits } from 'reka-ui';
import { cn } from '@/lib/utils';

defineOptions({ inheritAttrs: false });

const props = withDefaults(defineProps<PopoverContentProps & { class?: HTMLAttributes['class'] }>(), {
    sideOffset: 4,
});
const emits = defineEmits<PopoverContentEmits>();
const delegatedProps = reactiveOmit(props, 'class');
const forwarded = useForwardPropsEmits(delegatedProps, emits);

const classes = computed(() => cn('z-50 w-auto rounded-md border border-border bg-popover p-0 text-popover-foreground shadow-md outline-none', 'data-[state=open]:animate-in data-[state=closed]:animate-out', 'data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0', 'data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95', 'data-[side=bottom]:slide-in-from-top-2 data-[side=top]:slide-in-from-bottom-2', props.class))
</script>

<template>
    <PopoverPortal>
        <PopoverContent
            v-bind="{ ...$attrs, ...forwarded }"
            :class="classes"
        >
            <slot />
        </PopoverContent>
    </PopoverPortal>
</template>
