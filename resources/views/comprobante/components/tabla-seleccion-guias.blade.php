@forelse ($guias as $guia)
    <tr>
        <td>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" value="{{ $guia->id }}" id="guia-seleccion-{{ $guia->id }}">
                <label class="custom-control-label" for="guia-seleccion-{{$guia->id}}"></label>
            </div>
        </td>
        <td>
            {{ $guia->nro_guia_concatenado }}
        </td>
        <td>
            {{ $guia->fech_emis }}
        </td>
        <td>
            {{ $guia->comprobante_sunat }}
        </td>
    </tr>
@empty
    <x-non-result-table/>
@endforelse
