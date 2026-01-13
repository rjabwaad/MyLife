<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Finance Tracker - MyLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">💰 Finance Tracker</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                        🏠 Accueil
                    </a>
                    <span class="text-sm">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success Message -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
                <p class="font-bold">✅ Succès</p>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Daily Income -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Revenus du jour</p>
                        <h3 class="text-3xl font-bold text-green-600 mt-2">{{ number_format($dailyIncome, 2) }} DH</h3>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full">
                        <span class="text-3xl">💵</span>
                    </div>
                </div>
            </div>

            <!-- Daily Expense -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Dépenses du jour</p>
                        <h3 class="text-3xl font-bold text-red-600 mt-2">{{ number_format($dailyExpense, 2) }} DH</h3>
                    </div>
                    <div class="bg-red-100 p-4 rounded-full">
                        <span class="text-3xl">💸</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Income -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Revenus du mois</p>
                        <h3 class="text-3xl font-bold text-blue-600 mt-2">{{ number_format($monthlyIncome, 2) }} DH</h3>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                        <span class="text-3xl">📈</span>
                    </div>
                </div>
            </div>

            <!-- Monthly Expense -->
            <div class="bg-white rounded-xl shadow-lg p-6 card-hover transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Dépenses du mois</p>
                        <h3 class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($monthlyExpense, 2) }} DH</h3>
                    </div>
                    <div class="bg-orange-100 p-4 rounded-full">
                        <span class="text-3xl">📉</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl shadow-2xl p-8 mb-8 text-white">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-sm opacity-90">Balance du mois</p>
                    <h2 class="text-4xl font-bold mt-2">{{ number_format($monthlyIncome - $monthlyExpense, 2) }} DH</h2>
                </div>
                <div class="text-center">
                    <p class="text-sm opacity-90">Total des dettes</p>
                    <h2 class="text-4xl font-bold mt-2">{{ number_format($totalDebt, 2) }} DH</h2>
                </div>
                <div class="text-center">
                    <p class="text-sm opacity-90">Total payé</p>
                    <h2 class="text-4xl font-bold mt-2">{{ number_format($totalPaid, 2) }} DH</h2>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Income Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">💵 Revenus</h2>
                    <button onclick="openIncomeModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        ➕ Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($incomes as $income)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $income->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $income->source }}</td>
                                    <td class="px-4 py-3 text-sm text-green-600 font-bold">+{{ number_format($income->amount, 2) }} DH</td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('finance.income.delete', $income->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr?')" class="text-red-600 hover:text-red-800">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Aucun revenu enregistré
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expense Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">💸 Dépenses</h2>
                    <button onclick="openExpenseModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        ➕ Ajouter
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($expenses as $expense)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $expense->date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">{{ $expense->name }}</td>
                                    <td class="px-4 py-3 text-sm text-red-600 font-bold">-{{ number_format($expense->amount, 2) }} DH</td>
                                    <td class="px-4 py-3 text-sm">
                                        <form action="{{ route('finance.expense.delete', $expense->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Êtes-vous sûr?')" class="text-red-600 hover:text-red-800">
                                                🗑️
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Aucune dépense enregistrée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Debts Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mt-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">💳 Suivi des Dettes</h2>
                <button onclick="openDebtModal()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                    ➕ Ajouter
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($debts as $debt)
                    <div class="border-2 border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300
                        {{ $debt->status === 'paid' ? 'bg-green-50 border-green-300' : ($debt->status === 'partial' ? 'bg-yellow-50 border-yellow-300' : 'bg-red-50 border-red-300') }}">

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $debt->creditor }}</h3>
                                <p class="text-sm text-gray-500">
                                    @if($debt->due_date)
                                        Échéance: {{ $debt->due_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $debt->status === 'paid' ? 'bg-green-200 text-green-800' : ($debt->status === 'partial' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                {{ $debt->status === 'paid' ? '✅ Payé' : ($debt->status === 'partial' ? '⏳ Partiel' : '❌ En attente') }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-bold">{{ number_format($debt->total_amount, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Payé:</span>
                                <span class="font-bold text-green-600">{{ number_format($debt->paid_amount, 2) }} DH</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Restant:</span>
                                <span class="font-bold text-red-600">{{ number_format($debt->remaining_amount, 2) }} DH</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($debt->paid_amount / $debt->total_amount) * 100 }}%"></div>
                        </div>

                        <div class="flex space-x-2">
                            <button onclick="openUpdateDebtModal({{ $debt->id }}, {{ $debt->total_amount }}, {{ $debt->paid_amount }})"
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                💰 Payer
                            </button>
                            <form action="{{ route('finance.debt.delete', $debt->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr?')"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <p class="text-xl">🎉 Aucune dette enregistrée!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Wishlist Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mt-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">🎁 Liste de Souhaits</h2>
                <button onclick="openWishlistModal()" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition">
                    ➕ Ajouter
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($wishlists as $item)
                    <div class="border-2 rounded-xl p-6 hover:shadow-lg transition-all duration-300
                        {{ $item->purchased ? 'bg-gray-50 border-gray-300 opacity-75' : 'border-pink-200' }}">

                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-gray-800 {{ $item->purchased ? 'line-through' : '' }}">
                                {{ $item->item_name }}
                            </h3>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $item->priority === 'high' ? 'bg-red-200 text-red-800' : ($item->priority === 'medium' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800') }}">
                                {{ $item->priority === 'high' ? '🔥 Haute' : ($item->priority === 'medium' ? '⚡ Moyenne' : '✨ Basse') }}
                            </span>
                        </div>

                        @if($item->price)
                            <p class="text-2xl font-bold text-pink-600 mb-2">{{ number_format($item->price, 2) }} DH</p>
                        @endif

                        @if($item->description)
                            <p class="text-sm text-gray-600 mb-4">{{ $item->description }}</p>
                        @endif

                        @if($item->url)
                            <a href="{{ $item->url }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm block mb-4">
                                🔗 Voir le produit
                            </a>
                        @endif

                        @if($item->purchased)
                            <p class="text-sm text-green-600 font-semibold mb-4">
                                ✅ Acheté le {{ $item->purchased_date->format('d/m/Y') }}
                            </p>
                        @endif

                        <div class="flex space-x-2">
                            @if(!$item->purchased)
                                <form action="{{ route('finance.wishlist.purchased', $item->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                        ✅ Marquer acheté
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('finance.wishlist.delete', $item->id) }}" method="POST" class="{{ $item->purchased ? 'w-full' : 'flex-1' }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr?')"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <p class="text-xl">📝 Aucun article dans votre liste de souhaits</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Income Modal -->
    <div id="incomeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">💵 Ajouter un Revenu</h3>
            <form action="{{ route('finance.income.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                        <input type="text" name="source" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant (DH)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="salary">Salaire</option>
                            <option value="freelance">Freelance</option>
                            <option value="investment">Investissement</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (optionnel)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        Ajouter
                    </button>
                    <button type="button" onclick="closeIncomeModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Expense Modal -->
    <div id="expenseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">💸 Ajouter une Dépense</h3>
            <form action="{{ route('finance.expense.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant (DH)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                        <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="food">Nourriture</option>
                            <option value="transport">Transport</option>
                            <option value="entertainment">Divertissement</option>
                            <option value="bills">Factures</option>
                            <option value="shopping">Shopping</option>
                            <option value="health">Santé</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (optionnel)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Ajouter
                    </button>
                    <button type="button" onclick="closeExpenseModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Debt Modal -->
    <div id="debtModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">💳 Ajouter une Dette</h3>
            <form action="{{ route('finance.debt.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Créancier</label>
                        <input type="text" name="creditor" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant Total (DH)</label>
                        <input type="number" step="0.01" name="total_amount" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant Payé (DH)</label>
                        <input type="number" step="0.01" name="paid_amount" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance (optionnel)</label>
                        <input type="date" name="due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (optionnel)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                        Ajouter
                    </button>
                    <button type="button" onclick="closeDebtModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Debt Modal -->
    <div id="updateDebtModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">💰 Mettre à jour le paiement</h3>
            <form id="updateDebtForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Montant Payé (DH)</label>
                        <input type="number" step="0.01" name="paid_amount" id="paidAmountInput" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Total: <span id="totalAmountDisplay"></span> DH</p>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                        Mettre à jour
                    </button>
                    <button type="button" onclick="closeUpdateDebtModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Wishlist Modal -->
    <div id="wishlistModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <h3 class="text-2xl font-bold mb-6">🎁 Ajouter à la Liste de Souhaits</h3>
            <form action="{{ route('finance.wishlist.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom de l'article</label>
                        <input type="text" name="item_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix (DH) - optionnel</label>
                        <input type="number" step="0.01" name="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                        <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="low">✨ Basse</option>
                            <option value="medium" selected>⚡ Moyenne</option>
                            <option value="high">🔥 Haute</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL (optionnel)</label>
                        <input type="url" name="url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (optionnel)</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"></textarea>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="flex-1 bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition">
                        Ajouter
                    </button>
                    <button type="button" onclick="closeWishlistModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Income Modal
        function openIncomeModal() {
            document.getElementById('incomeModal').classList.remove('hidden');
        }
        function closeIncomeModal() {
            document.getElementById('incomeModal').classList.add('hidden');
        }

        // Expense Modal
        function openExpenseModal() {
            document.getElementById('expenseModal').classList.remove('hidden');
        }
        function closeExpenseModal() {
            document.getElementById('expenseModal').classList.add('hidden');
        }

        // Debt Modal
        function openDebtModal() {
            document.getElementById('debtModal').classList.remove('hidden');
        }
        function closeDebtModal() {
            document.getElementById('debtModal').classList.add('hidden');
        }

        // Update Debt Modal
        function openUpdateDebtModal(debtId, totalAmount, paidAmount) {
            const modal = document.getElementById('updateDebtModal');
            const form = document.getElementById('updateDebtForm');
            const input = document.getElementById('paidAmountInput');
            const display = document.getElementById('totalAmountDisplay');

            form.action = `/finance/debt/${debtId}`;
            input.value = paidAmount;
            display.textContent = totalAmount;

            modal.classList.remove('hidden');
        }
        function closeUpdateDebtModal() {
            document.getElementById('updateDebtModal').classList.add('hidden');
        }

        // Wishlist Modal
        function openWishlistModal() {
            document.getElementById('wishlistModal').classList.remove('hidden');
        }
        function closeWishlistModal() {
            document.getElementById('wishlistModal').classList.add('hidden');
        }

        // Close modals on outside click
        window.onclick = function(event) {
            const modals = ['incomeModal', 'expenseModal', 'debtModal', 'updateDebtModal', 'wishlistModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }

        // Set today's date as default
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = document.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                if (!input.value && input.name !== 'due_date') {
                    input.value = today;
                }
            });
        });
    </script>

</body>
</html>

