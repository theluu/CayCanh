import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useSite() {
    const page = usePage();

    const site = computed(() => page.props.site ?? {});
    const telHref = computed(() => (site.value.hotline ? 'tel:' + site.value.hotline.replace(/[^0-9+]/g, '') : null));
    const zaloHref = computed(() => (site.value.zalo_phone ? 'https://zalo.me/' + site.value.zalo_phone.replace(/[^0-9]/g, '') : null));

    return { site, telHref, zaloHref };
}
