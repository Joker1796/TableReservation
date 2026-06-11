<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

const CONTAINER = 360;
const CROP = 280;
const MARGIN = (CONTAINER - CROP) / 2;

defineProps<{ src: string }>();
const emit = defineEmits<{ confirm: [blob: Blob]; cancel: [] }>();

const imgEl = ref<HTMLImageElement | null>(null);
const naturalW = ref(1);
const naturalH = ref(1);
const zoom = ref(1);
const offsetX = ref(0);
const offsetY = ref(0);

function onImgLoad(): void {
    if (!imgEl.value) {
return;
}

    naturalW.value = imgEl.value.naturalWidth;
    naturalH.value = imgEl.value.naturalHeight;
    zoom.value = 1;
    offsetX.value = 0;
    offsetY.value = 0;
}

const baseScale = computed(() => CROP / Math.min(naturalW.value, naturalH.value));
const displayScale = computed(() => baseScale.value * zoom.value);

function halfExcess(natural: number): number {
    return Math.max(0, (natural * displayScale.value - CROP) / 2);
}

function clampOffset(val: number, natural: number): number {
    const he = halfExcess(natural);

    return Math.max(-he, Math.min(he, val));
}

watch(zoom, () => {
    offsetX.value = clampOffset(offsetX.value, naturalW.value);
    offsetY.value = clampOffset(offsetY.value, naturalH.value);
});

const imageStyle = computed(() => ({
    position: 'absolute' as const,
    top: '50%',
    left: '50%',
    maxWidth: 'none',
    userSelect: 'none' as const,
    touchAction: 'none' as const,
    cursor: 'grab' as const,
    transform: `translate(calc(-50% + ${offsetX.value}px), calc(-50% + ${offsetY.value}px)) scale(${displayScale.value})`,
}));

let drag: { x: number; y: number; ox: number; oy: number } | null = null;

function onPointerDown(e: PointerEvent): void {
    drag = { x: e.clientX, y: e.clientY, ox: offsetX.value, oy: offsetY.value };
    (e.currentTarget as HTMLElement).setPointerCapture(e.pointerId);
    e.preventDefault();
}

function onPointerMove(e: PointerEvent): void {
    if (!drag) {
return;
}

    offsetX.value = clampOffset(drag.ox + e.clientX - drag.x, naturalW.value);
    offsetY.value = clampOffset(drag.oy + e.clientY - drag.y, naturalH.value);
}

function onPointerUp(): void {
    drag = null;
}

function onWheel(e: WheelEvent): void {
    zoom.value = Math.max(1, Math.min(3, zoom.value - e.deltaY * 0.001));
}

function confirm(): void {
    const img = imgEl.value;

    if (!img) {
return;
}

    const ds = displayScale.value;
    const imgLeft = CONTAINER / 2 - naturalW.value * ds / 2 + offsetX.value;
    const imgTop = CONTAINER / 2 - naturalH.value * ds / 2 + offsetY.value;
    const srcX = (MARGIN - imgLeft) / ds;
    const srcY = (MARGIN - imgTop) / ds;
    const srcSize = CROP / ds;

    const canvas = document.createElement('canvas');
    canvas.width = 512;
    canvas.height = 512;
    canvas.getContext('2d')!.drawImage(img, srcX, srcY, srcSize, srcSize, 0, 0, 512, 512);
    canvas.toBlob((blob) => {
        if (blob) {
emit('confirm', blob);
}
    }, 'image/jpeg', 0.9);
}
</script>

<template>
    <Dialog :open="true" @update:open="(val) => { if (!val) emit('cancel') }">
        <DialogContent class="sm:max-w-[420px]" :show-close-button="false">
            <DialogHeader>
                <DialogTitle>Обрезать фото</DialogTitle>
            </DialogHeader>

            <div class="flex flex-col items-center gap-4">
                <div
                    class="relative overflow-hidden rounded-md bg-black"
                    :style="{ width: `${CONTAINER}px`, height: `${CONTAINER}px` }"
                    @pointerdown="onPointerDown"
                    @pointermove="onPointerMove"
                    @pointerup="onPointerUp"
                    @wheel.prevent="onWheel"
                >
                    <img ref="imgEl" :src="src" draggable="false" :style="imageStyle" @load="onImgLoad" />

                    <svg class="pointer-events-none absolute inset-0" :width="CONTAINER" :height="CONTAINER">
                        <defs>
                            <mask id="avatar-crop-mask">
                                <rect :width="CONTAINER" :height="CONTAINER" fill="white" />
                                <rect :x="MARGIN" :y="MARGIN" :width="CROP" :height="CROP" fill="black" />
                            </mask>
                        </defs>
                        <rect :width="CONTAINER" :height="CONTAINER" fill="rgba(0,0,0,0.55)" mask="url(#avatar-crop-mask)" />
                    </svg>

                    <div
                        class="pointer-events-none absolute border border-white/60"
                        :style="{ left: `${MARGIN}px`, top: `${MARGIN}px`, width: `${CROP}px`, height: `${CROP}px` }"
                    />
                </div>

                <div class="flex w-full items-center gap-3 px-1">
                    <span class="text-xs text-muted-foreground select-none">−</span>
                    <input
                        v-model.number="zoom"
                        type="range"
                        min="1"
                        max="3"
                        step="0.01"
                        class="h-1.5 w-full cursor-pointer appearance-none rounded-full bg-muted [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-primary"
                    />
                    <span class="text-xs text-muted-foreground select-none">+</span>
                </div>
            </div>

            <DialogFooter>
                <Button variant="outline" @click="emit('cancel')">Отмена</Button>
                <Button @click="confirm">Применить</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
