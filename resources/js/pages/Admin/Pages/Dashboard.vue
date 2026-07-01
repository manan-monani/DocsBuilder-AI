<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage, Link, Head } from '@inertiajs/vue3';
import {
    TrendingUp,
    TrendingDown,
    Users,
    FileText,
    FolderOpen,
    Activity,
    ArrowUpRight,
    Clock,
    CheckCircle2,
    AlertCircle,
    Layers,
    BookOpen,
    Settings,
    Plus,
    Zap,
    BarChart3,
    PieChart,
    Calendar
} from 'lucide-vue-next';
import { computed, markRaw } from 'vue';
import admin from '@/routes/admin';

const page = usePage();

// Props from backend
const props = defineProps<{
    stats: {
        users: number;
        projects: number;
        versions: number;
        categories: number;
        pages: number;
        published_pages: number;
    };
    recentActivity: Array<{
        id: number;
        user: string;
        description: string;
        event: string;
        subject_type: string;
        subject_id: number;
        created_at: string;
    }>;
}>();

// Get user info
const user = computed(() => (page.props as any).auth?.user);
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good Morning';
    if (hour < 17) return 'Good Afternoon';
    return 'Good Evening';
});

// Stats cards with real data
const statsCards = computed(() => [
    {
        title: 'Total Users',
        value: props.stats?.users?.toLocaleString() || '0',
        change: 'Active accounts',
        trend: 'up',
        icon: markRaw(Users),
        color: 'indigo',
        bgGradient: 'from-indigo-500 to-indigo-600',
    },
    {
        title: 'Documentation Pages',
        value: props.stats?.pages?.toString() || '0',
        change: `${props.stats?.published_pages || 0} published`,
        trend: 'up',
        icon: markRaw(FileText),
        color: 'emerald',
        bgGradient: 'from-emerald-500 to-emerald-600',
    },
    {
        title: 'Active Projects',
        value: props.stats?.projects?.toString() || '0',
        change: `${props.stats?.versions || 0} versions`,
        trend: 'up',
        icon: markRaw(FolderOpen),
        color: 'amber',
        bgGradient: 'from-amber-500 to-amber-600',
    },
    {
        title: 'Categories',
        value: props.stats?.categories?.toString() || '0',
        change: 'Organized',
        trend: 'up',
        icon: markRaw(Layers),
        color: 'sky',
        bgGradient: 'from-sky-500 to-sky-600',
    },
]);

// Quick actions
const quickActions = [
    {
        title: 'Add User',
        description: 'Create new user account',
        icon: markRaw(Users),
        href: admin.users.create.url(),
        color: 'indigo',
    },
    {
        title: 'New Project',
        description: 'Start a documentation project',
        icon: markRaw(FolderOpen),
        href: admin.docs.projects.create.url(),
        color: 'emerald',
    },
    {
        title: 'Manage Roles',
        description: 'Configure user permissions',
        icon: markRaw(Settings),
        href: admin.roles.index.url(),
        color: 'amber',
    },
    {
        title: 'View Logs',
        description: 'Check system activity',
        icon: markRaw(Activity),
        href: admin.activity_logs.index.url(),
        color: 'rose',
    },
];

// Icon and color mapping for activity events
const getActivityMeta = (event: string) => {
    switch (event) {
        case 'created':
            return { icon: markRaw(Plus), color: 'emerald' };
        case 'updated':
            return { icon: markRaw(Settings), color: 'amber' };
        case 'deleted':
            return { icon: markRaw(AlertCircle), color: 'rose' };
        case 'login':
            return { icon: markRaw(Users), color: 'indigo' };
        default:
            return { icon: markRaw(Activity), color: 'sky' };
    }
};

// Process recent activity from backend
const activityList = computed(() => {
    return (props.recentActivity || []).map(item => ({
        ...item,
        ...getActivityMeta(item.event),
    }));
});

// System overview
const systemOverview = [
    { label: 'Server Status', value: 'Online', status: 'success' },
    { label: 'Database', value: 'Connected', status: 'success' },
    { label: 'Cache', value: 'Active', status: 'success' },
    { label: 'Queue', value: 'Running', status: 'success' },
];
</script>

<template>
    <Head title="Dashboard" />
    <AdminLayout>
        <div class="p-6 space-y-6 animate-fade-in">

            <!-- Welcome Header -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ greeting }}, {{ user?.name || 'Admin' }}! 👋
                    </h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        Here's what's happening with your system today.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <Calendar class="w-4 h-4 text-gray-400" />
                        <span class="text-sm text-gray-600 dark:text-gray-300">{{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="(stat, index) in statsCards"
                    :key="index"
                    class="group relative bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 overflow-hidden"
                >
                    <!-- Background decoration -->
                    <div :class="`absolute -top-10 -right-10 w-32 h-32 bg-gradient-to-br ${stat.bgGradient} rounded-full opacity-10 group-hover:opacity-20 transition-opacity`"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div :class="`p-3 rounded-xl bg-gradient-to-br ${stat.bgGradient} text-white shadow-lg`">
                                <component :is="stat.icon" class="w-5 h-5" />
                            </div>
                            <div
                                class="flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full"
                                :class="stat.trend === 'up'
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                    : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'"
                            >
                                <component :is="stat.trend === 'up' ? TrendingUp : TrendingDown" class="w-3 h-3" />
                                {{ stat.change }}
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ stat.title }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stat.value }}</p>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Quick Actions -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Quick Actions</h2>
                        <Zap class="w-5 h-5 text-amber-500" />
                    </div>

                    <div class="space-y-3">
                        <Link
                            v-for="(action, index) in quickActions"
                            :key="index"
                            :href="action.href"
                            class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-all group"
                        >
                            <div
                                :class="`p-2.5 rounded-lg bg-${action.color}-100 dark:bg-${action.color}-900/30 text-${action.color}-600 dark:text-${action.color}-400 group-hover:scale-110 transition-transform`"
                            >
                                <component :is="action.icon" class="w-5 h-5" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ action.title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                            </div>
                            <ArrowUpRight class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" />
                        </Link>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h2>
                        <Link :href="admin.activity_logs.index.url()" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            View All
                        </Link>
                    </div>

                    <div v-if="activityList.length > 0" class="space-y-4">
                        <div
                            v-for="activity in activityList"
                            :key="activity.id"
                            class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-colors"
                        >
                            <div
                                :class="`p-2 rounded-lg bg-${activity.color}-100 dark:bg-${activity.color}-900/30 text-${activity.color}-600 dark:text-${activity.color}-400 flex-shrink-0`"
                            >
                                <component :is="activity.icon" class="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ activity.user }}</span>
                                    <span class="ml-1">{{ activity.description }}</span>
                                    <span v-if="activity.subject_type" class="font-medium text-indigo-600 dark:text-indigo-400 ml-1">{{ activity.subject_type }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <Clock class="w-3 h-3" />
                                    {{ activity.created_at }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-10">
                        <Activity class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity to display</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- System Overview -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">System Overview</h2>
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="text-xs font-bold uppercase tracking-wider">All Systems Operational</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-for="(item, index) in systemOverview"
                            :key="index"
                            class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50"
                        >
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ item.label }}</span>
                            <span
                                class="flex items-center gap-1.5 text-sm font-semibold"
                                :class="item.status === 'success' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                            >
                                <CheckCircle2 v-if="item.status === 'success'" class="w-4 h-4" />
                                <AlertCircle v-else class="w-4 h-4" />
                                {{ item.value }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Documentation Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Documentation</h2>
                        <Link :href="admin.docs.projects.index.url()" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            Manage Docs
                        </Link>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20">
                            <FolderOpen class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mx-auto mb-2" />
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats?.projects || 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Projects</p>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20">
                            <Layers class="w-6 h-6 text-emerald-600 dark:text-emerald-400 mx-auto mb-2" />
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats?.versions || 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Versions</p>
                        </div>
                        <div class="text-center p-4 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20">
                            <FileText class="w-6 h-6 text-amber-600 dark:text-amber-400 mx-auto mb-2" />
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ props.stats?.pages || 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Pages</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link
                            :href="admin.docs.pages.create.url()"
                            class="flex items-center justify-center gap-2 w-full py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-xl transition-all shadow-lg shadow-indigo-600/20"
                        >
                            <Plus class="w-4 h-4" />
                            Create New Page
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
