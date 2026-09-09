import { onUnmounted, ref, watch, type Ref } from 'vue'

/**
 * Attaches HTML5 file-drop handlers to the given element ref and exposes a
 * single `isDragOver` flag that can drive an overlay.
 *
 * - Uses a drag-enter / leave *counter* so nested children don't flicker the
 *   overlay (a known DOM annoyance).
 * - Ignores drags that are not carrying files (text selections, etc.).
 * - Auto-detaches when the target ref changes or the component unmounts.
 *
 * @param target  Template ref to the drop-zone element.
 * @param onDrop  Called with the dropped File[] (never empty).
 */
export function useFileDropZone(
  target: Ref<HTMLElement | null>,
  onDrop: (files: File[]) => void,
): { isDragOver: Ref<boolean> } {
  const isDragOver = ref(false)
  let dragCounter = 0
  let attachedEl: HTMLElement | null = null

  function preventDefaults(e: Event): void {
    e.preventDefault()
    e.stopPropagation()
  }

  function isFileDrag(e: DragEvent): boolean {
    if (!e.dataTransfer) return false
    
    return Array.from(e.dataTransfer.types).includes('Files')
  }

  function onEnter(e: DragEvent): void {
    if (!isFileDrag(e)) return
    preventDefaults(e)
    dragCounter += 1
    isDragOver.value = true
  }

  function onOver(e: DragEvent): void {
    if (!isFileDrag(e)) return
    preventDefaults(e)
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy'
  }

  function onLeave(e: DragEvent): void {
    if (!isFileDrag(e)) return
    preventDefaults(e)
    dragCounter = Math.max(0, dragCounter - 1)
    if (dragCounter === 0) isDragOver.value = false
  }

  function onDropEvt(e: DragEvent): void {
    if (!isFileDrag(e)) return
    preventDefaults(e)
    dragCounter = 0
    isDragOver.value = false

    const files = Array.from(e.dataTransfer?.files ?? [])
    if (files.length > 0) onDrop(files)
  }

  function attach(el: HTMLElement): void {
    el.addEventListener('dragenter', onEnter)
    el.addEventListener('dragover',  onOver)
    el.addEventListener('dragleave', onLeave)
    el.addEventListener('drop',      onDropEvt)
    attachedEl = el
  }

  function detach(): void {
    if (!attachedEl) return
    attachedEl.removeEventListener('dragenter', onEnter)
    attachedEl.removeEventListener('dragover',  onOver)
    attachedEl.removeEventListener('dragleave', onLeave)
    attachedEl.removeEventListener('drop',      onDropEvt)
    attachedEl = null
    dragCounter = 0
    isDragOver.value = false
  }

  watch(target, el => {
    detach()
    if (el) attach(el)
  }, { immediate: true })

  onUnmounted(detach)

  return { isDragOver }
}
