@extends('layouts.app')

@section('title', 'Tutoriais')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('help.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tutoriais</h1>
                    <p class="mt-2 text-gray-600">Aprenda passo a passo como usar todas as funcionalidades</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-4">
                <select id="category-filter" class="form-select">
                    <option value="">Todas as categorias</option>
                    <option value="Básico">Básico</option>
                    <option value="Avançado">Avançado</option>
                    <option value="Design">Design</option>
                    <option value="Premium">Premium</option>
                    <option value="Analytics">Analytics</option>
                    <option value="Colaboração">Colaboração</option>
                </select>
                
                <select id="difficulty-filter" class="form-select">
                    <option value="">Todas as dificuldades</option>
                    <option value="Iniciante">Iniciante</option>
                    <option value="Intermediário">Intermediário</option>
                    <option value="Avançado">Avançado</option>
                </select>
                
                <button onclick="applyFilters()" class="btn-outline">
                    Filtrar
                </button>
            </div>
        </div>

        <!-- Lista de Tutoriais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tutorials as $index => $tutorial)
                <div class="tutorial-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                     data-category="{{ $tutorial['category'] }}" 
                     data-difficulty="{{ $tutorial['difficulty'] }}">
                    <div class="p-6">
                        <!-- Header do Tutorial -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $tutorial['title'] }}</h3>
                                <p class="text-gray-600 text-sm">{{ $tutorial['description'] }}</p>
                            </div>
                        </div>

                        <!-- Meta informações -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $tutorial['category'] }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $tutorial['difficulty'] }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $tutorial['duration'] }}
                            </span>
                        </div>

                        <!-- Passos do Tutorial -->
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Passos:</h4>
                            <ol class="text-sm text-gray-600 space-y-1">
                                @foreach($tutorial['steps'] as $stepIndex => $step)
                                    <li class="flex items-start">
                                        <span class="flex-shrink-0 w-5 h-5 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-xs font-medium mr-2 mt-0.5">
                                            {{ $stepIndex + 1 }}
                                        </span>
                                        <span>{{ $step }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <!-- Botão de Ação -->
                        <div class="flex justify-between items-center">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $tutorial['duration'] }}
                            </div>
                            <button onclick="startTutorial({{ $index }})" class="btn-primary text-sm">
                                Começar Tutorial
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tutorial Detalhado Modal -->
        <div id="tutorial-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="tutorial-title" class="text-2xl font-bold text-gray-900"></h3>
                        <button onclick="closeTutorial()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div id="tutorial-content" class="space-y-6">
                        <!-- Conteúdo será preenchido via JavaScript -->
                    </div>
                    
                    <div class="mt-8 flex justify-between">
                        <button onclick="previousStep()" id="prev-btn" class="btn-outline" style="display: none;">
                            Anterior
                        </button>
                        <div class="flex space-x-2">
                            <button onclick="closeTutorial()" class="btn-outline">
                                Fechar
                            </button>
                            <button onclick="nextStep()" id="next-btn" class="btn-primary">
                                Próximo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentTutorial = null;
let currentStep = 0;

function applyFilters() {
    const categoryFilter = document.getElementById('category-filter').value;
    const difficultyFilter = document.getElementById('difficulty-filter').value;
    const cards = document.querySelectorAll('.tutorial-card');
    
    cards.forEach(card => {
        const category = card.dataset.category;
        const difficulty = card.dataset.difficulty;
        
        let show = true;
        
        if (categoryFilter && category !== categoryFilter) {
            show = false;
        }
        
        if (difficultyFilter && difficulty !== difficultyFilter) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

function startTutorial(index) {
    currentTutorial = @json($tutorials)[index];
    currentStep = 0;
    
    document.getElementById('tutorial-title').textContent = currentTutorial.title;
    document.getElementById('tutorial-modal').classList.remove('hidden');
    
    showStep();
}

function showStep() {
    const content = document.getElementById('tutorial-content');
    const step = currentTutorial.steps[currentStep];
    
    content.innerHTML = `
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center mb-3">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">
                    ${currentStep + 1}
                </span>
                <h4 class="text-lg font-medium text-blue-900">Passo ${currentStep + 1} de ${currentTutorial.steps.length}</h4>
            </div>
            <p class="text-blue-800">${step}</p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="font-medium text-gray-900 mb-2">Dica:</h5>
            <p class="text-gray-700 text-sm">Siga os passos na ordem indicada para obter os melhores resultados.</p>
        </div>
    `;
    
    // Controle dos botões
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    prevBtn.style.display = currentStep > 0 ? 'block' : 'none';
    
    if (currentStep === currentTutorial.steps.length - 1) {
        nextBtn.textContent = 'Finalizar';
        nextBtn.onclick = closeTutorial;
    } else {
        nextBtn.textContent = 'Próximo';
        nextBtn.onclick = nextStep;
    }
}

function nextStep() {
    if (currentStep < currentTutorial.steps.length - 1) {
        currentStep++;
        showStep();
    }
}

function previousStep() {
    if (currentStep > 0) {
        currentStep--;
        showStep();
    }
}

function closeTutorial() {
    document.getElementById('tutorial-modal').classList.add('hidden');
    currentTutorial = null;
    currentStep = 0;
}

// Fechar modal ao clicar fora
document.getElementById('tutorial-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTutorial();
    }
});
</script>
@endsection
