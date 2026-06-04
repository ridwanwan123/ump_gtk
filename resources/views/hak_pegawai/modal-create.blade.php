<div class="modal fade" id="modalHak">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form method="POST" action="{{ route('hak-pembayaran.store') }}">
                @csrf

                <div class="modal-header">
                    <h5>Input Hak Pembayaran</h5>
                </div>

                <div class="modal-body table-responsive">

                    <table class="table table-bordered text-center">

                        <thead>
                            <tr>
                                <th>Nama</th>

                                @foreach($bulan as $b)
                                    <th>{{ \Carbon\Carbon::create()->month($b)->format('M') }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($pegawai as $p)

                                <tr>
                                    <td class="text-start">
                                        {{ $p->nama_rekening }}
                                        <input type="hidden" name="pegawai_id[]" value="{{ $p->id }}">
                                    </td>

                                    @foreach($bulan as $b)

                                        <td>
                                            <input type="checkbox"
                                                name="hak[{{ $p->id }}][{{ $b }}]"
                                                value="1">
                                        </td>

                                    @endforeach
                                </tr>

                            @endforeach
                        </tbody>

                    </table>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>