@extends('layouts.app')

@section('content')
    <h2>Envoyer un document à plusieurs signataires</h2>

    <form method="POST" action="{{ route('zoho.send.dynamic') }}">
        @csrf

        <div class="form-group">
            <label for="document_name">Nom du document</label>
            <input type="text" name="document_name" class="form-control" value="Document à signer - {{ now()->format('Y-m-d H:i') }}">
        </div>

        <div class="form-group">
            <label for="is_sequential">Signature séquentielle ?</label>
            <select name="is_sequential" class="form-control">
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>
        </div>

        <hr>
        <div class="form-group">
            <label for="number_of_recipients">Nombre de signataires</label>
            <input type="number" id="number_of_recipients" class="form-control" min="1" value="2">
        </div>

        <div id="recipients-container"></div>

        <button type="submit" class="btn btn-primary mt-3">Envoyer à signer</button>
    </form>
@endsection

@section('scripts')
<script>
    const container = document.getElementById('recipients-container');
    const inputNumber = document.getElementById('number_of_recipients');

    function generateRecipientFields(n) {
        container.innerHTML = '';
        for (let i = 0; i < n; i++) {
            container.innerHTML += `
                <div class="card mb-3 p-3">
                    <h5>Signataire ${i + 1}</h5>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="recipients[${i}][name]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="recipients[${i}][email]" class="form-control" required>
                    </div>
                </div>
            `;
        }
    }

    inputNumber.addEventListener('input', () => {
        const value = parseInt(inputNumber.value);
        if (value > 0) generateRecipientFields(value);
    });

    // Initial rendering
    generateRecipientFields(parseInt(inputNumber.value));
</script>
@endsection
