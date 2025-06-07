<script setup lang="ts">
import { ref, computed } from 'vue';
import CategoryFilter from './CategoryFilter.vue';
import { useEventBus } from '@/composables/useEventBus';
import { 
  Filter, 
  SortAsc, 
  SortDesc, 
  BookOpen, 
  LayoutGrid, 
  LayoutList 
} from 'lucide-vue-next';

const props = defineProps<{
  categories: string[];
  selectedCategory: string;
  totalBooks: number;
  filteredCount: number;
}>();

const emit = defineEmits<{
  (e: 'filter-category', category: string): void;
  (e: 'filter-availability', available: boolean | null): void;
  (e: 'sort', field: string, direction: 'asc' | 'desc'): void;
  (e: 'view-change', view: 'grid' | 'list'): void;
}>();

const eventBus = useEventBus();
const isFilterPanelOpen = ref(false);
const selectedAvailability = ref<boolean | null>(null);
const sortField = ref('title');
const sortDirection = ref<'asc' | 'desc'>('asc');
const viewMode = ref<'grid' | 'list'>('grid');

// Sort options
const sortOptions = [
  { label: 'Judul', value: 'title' },
  { label: 'Penulis', value: 'author' },
  { label: 'Kategori', value: 'category' }
];

// Handle category selection
function handleCategorySelect(category: string) {
  emit('filter-category', category);
}

// Handle availability filter
function setAvailabilityFilter(available: boolean | null) {
  selectedAvailability.value = available;
  emit('filter-availability', available);
}

// Handle sorting
function handleSort(field: string) {
  if (sortField.value === field) {
    // Toggle direction if same field
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  emit('sort', sortField.value, sortDirection.value);
}

// Handle view mode change
function changeViewMode(mode: 'grid' | 'list') {
  viewMode.value = mode;
  emit('view-change', mode);
}

// Toggle filter panel visibility
function toggleFilterPanel() {
  isFilterPanelOpen.value = !isFilterPanelOpen.value;
}

// Reset all filters
function resetFilters() {
  handleCategorySelect('Semua');
  setAvailabilityFilter(null);
  sortField.value = 'title';
  sortDirection.value = 'asc';
  eventBus.emit('search', '');
}
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
    <!-- Filter Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <Filter class="h-5 w-5 text-blue-600" />
        <h3 class="text-lg font-medium text-gray-900">Filter & Urutan</h3>
      </div>
      
      <div class="flex items-center gap-3">
        <!-- View Mode Toggles -->
        <div class="hidden md:flex items-center gap-2 border rounded-lg overflow-hidden">
          <button 
            @click="changeViewMode('grid')" 
            class="p-2 transition-colors"
            :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
          >
            <LayoutGrid class="h-4 w-4" />
          </button>
          <button 
            @click="changeViewMode('list')" 
            class="p-2 transition-colors"
            :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
          >
            <LayoutList class="h-4 w-4" />
          </button>
        </div>
        
        <!-- Mobile Toggle Button -->
        <button 
          @click="toggleFilterPanel" 
          class="md:hidden flex items-center gap-1 text-sm text-blue-600 border border-blue-300 rounded-md px-3 py-1 hover:bg-blue-50"
        >
          <Filter class="h-4 w-4" />
          {{ isFilterPanelOpen ? 'Tutup Filter' : 'Filter' }}
        </button>
        
        <!-- Reset Button -->
        <button
          @click="resetFilters"
          class="text-sm text-blue-600 border border-blue-300 rounded-md px-3 py-1 hover:bg-blue-50"
        >
          Reset
        </button>
      </div>
    </div>
    
    <!-- Mobile Filter Panel -->
    <div 
      v-if="isFilterPanelOpen"
      class="md:hidden space-y-4 pb-4 border-b border-gray-200"
    >
      <div>
        <p class="text-sm font-medium text-gray-700 mb-2">Kategori</p>
        <CategoryFilter
          :categories="categories"
          :selectedCategory="selectedCategory"
          @select="handleCategorySelect"
        />
      </div>
      
      <div>
        <p class="text-sm font-medium text-gray-700 mb-2">Ketersediaan</p>
        <div class="flex flex-wrap gap-2">
          <button
            @click="setAvailabilityFilter(null)"
            class="px-3 py-1 text-sm rounded-full border"
            :class="selectedAvailability === null ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
          >
            Semua
          </button>
          <button
            @click="setAvailabilityFilter(true)"
            class="px-3 py-1 text-sm rounded-full border"
            :class="selectedAvailability === true ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
          >
            Tersedia
          </button>
          <button
            @click="setAvailabilityFilter(false)"
            class="px-3 py-1 text-sm rounded-full border"
            :class="selectedAvailability === false ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
          >
            Tidak Tersedia
          </button>
        </div>
      </div>
      
      <div>
        <p class="text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="option in sortOptions"
            :key="option.value"
            @click="handleSort(option.value)"
            class="px-3 py-1 text-sm rounded-full border flex items-center gap-1"
            :class="sortField === option.value ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
          >
            {{ option.label }}
            <SortAsc v-if="sortField === option.value && sortDirection === 'asc'" class="h-3 w-3" />
            <SortDesc v-if="sortField === option.value && sortDirection === 'desc'" class="h-3 w-3" />
          </button>
        </div>
      </div>
    </div>
    
    <!-- Desktop Filter Layout -->
    <div class="hidden md:flex items-center justify-between">
      <div class="flex items-center gap-6">
        <!-- Category Filter -->
        <div class="w-64">
          <p class="text-sm font-medium text-gray-700 mb-2">Kategori</p>
          <CategoryFilter
            :categories="categories"
            :selectedCategory="selectedCategory"
            @select="handleCategorySelect"
          />
        </div>
        
        <!-- Availability Filter -->
        <div>
          <p class="text-sm font-medium text-gray-700 mb-2">Ketersediaan</p>
          <div class="flex gap-2">
            <button
              @click="setAvailabilityFilter(null)"
              class="px-3 py-1 text-sm rounded-full border"
              :class="selectedAvailability === null ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              Semua
            </button>
            <button
              @click="setAvailabilityFilter(true)"
              class="px-3 py-1 text-sm rounded-full border flex items-center gap-1"
              :class="selectedAvailability === true ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              <BookOpen class="h-4 w-4" />
              Tersedia
            </button>
            <button
              @click="setAvailabilityFilter(false)"
              class="px-3 py-1 text-sm rounded-full border"
              :class="selectedAvailability === false ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              Tidak Tersedia
            </button>
          </div>
        </div>
        
        <!-- Sort Options -->
        <div>
          <p class="text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</p>
          <div class="flex gap-2">
            <button
              v-for="option in sortOptions"
              :key="option.value"
              @click="handleSort(option.value)"
              class="px-3 py-1 text-sm rounded-full border flex items-center gap-1"
              :class="sortField === option.value ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              {{ option.label }}
              <SortAsc v-if="sortField === option.value && sortDirection === 'asc'" class="h-3 w-3" />
              <SortDesc v-if="sortField === option.value && sortDirection === 'desc'" class="h-3 w-3" />
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Result Summary -->
    <div class="mt-4 text-sm text-gray-500">
      Menampilkan {{ filteredCount }} dari {{ totalBooks }} buku
    </div>
  </div>
</template> 