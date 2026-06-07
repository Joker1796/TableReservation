<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { computed } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const classes = computed(() => cn('flex min-h-0 flex-1 flex-col gap-2 overflow-auto group-data-[collapsible=icon]:overflow-hidden', props.class))
</script>

<template>
  <div
    data-slot="sidebar-content"
    data-sidebar="content"
    :class="classes"
  >
    <slot />
  </div>
</template>
