<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { computed } from "vue"
import { cn } from "@/lib/utils"

const props = defineProps<{
  class?: HTMLAttributes["class"]
}>()

const classes = computed(() => cn('text-muted-foreground col-start-2 grid justify-items-start gap-1 text-sm [&_p]:leading-relaxed', props.class))
</script>

<template>
  <div
    data-slot="alert-description"
    :class="classes"
  >
    <slot />
  </div>
</template>
