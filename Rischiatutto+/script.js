// Game state
let questions = [];
let currentCell = null;

// DOM elements
const questionModal = document.getElementById('questionModal');
const questionType = document.getElementById('questionType');
const questionValue = document.getElementById('questionValue');
const questionText = document.getElementById('questionText');
const correctBtn = document.getElementById('correctBtn');
const incorrectBtn = document.getElementById('incorrectBtn');

// Load questions from JSON
async function fetchQuestions() {
    try {
        const response = await fetch('questions.json');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        questions = await response.json();
        setupEventListeners();
    } catch (error) {
        console.error('Error loading questions:', error);
        alert('Errore nel caricamento delle domande. Controlla la console per i dettagli.');
    }
}

// Set up event listeners for grid cells
function setupEventListeners() {
    const cells = document.querySelectorAll('.cell');
    cells.forEach(cell => {
        cell.addEventListener('click', function() {
            currentCell = this;
            const category = parseInt(this.dataset.category);
            const level = parseInt(this.dataset.level);
            showQuestion(category, level);
        });
    });
}

// Show question modal
function showQuestion(category, level) {
    const question = questions.find(q => 
        q.category === category && q.level === level
    );
    
    if (!question) {
        alert(`Nessuna domanda trovata per categoria ${category}, livello ${level}`);
        return;
    }
    
    questionType.textContent = question.type.toUpperCase();
    questionValue.textContent = `${level * 100} punti`;
    questionText.textContent = question.text;
    questionModal.style.display = 'flex';
}

// Handle answer selection
function handleAnswer(isCorrect) {
    if (currentCell === null) return;
    
    currentCell.classList.remove('correct', 'incorrect');
    currentCell.classList.add(isCorrect ? 'correct' : 'incorrect');
    questionModal.style.display = 'none';
    currentCell = null;
}

// Event listeners
correctBtn.addEventListener('click', () => handleAnswer(true));
incorrectBtn.addEventListener('click', () => handleAnswer(false));

// Close modal if clicked outside content
questionModal.addEventListener('click', (e) => {
    if (e.target === questionModal) {
        questionModal.style.display = 'none';
        currentCell = null;
    }
});

// Initialize game
document.addEventListener('DOMContentLoaded', fetchQuestions);
