<?php

namespace App\Filament\Resources\Invoices;

use App\Filament\Resources\Invoices\Pages\ManageInvoices;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\IconColumn;
use Filament\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Sum as SummarizersSum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Invoice';
    protected static ?string $pluralLabel = 'Invoices';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),

                TextInput::make('invoice_number')
                    ->label('Nomor Invoice')
                    ->disabled()
                    ->dehydrated()
                    ->default(function () {
                        return Invoice::generateInvoiceNumber();
                    }),

                DatePicker::make('invoice_date')
                    ->label('Tanggal Invoice')
                    ->default(now())
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Jatuh Tempo'),

                TextInput::make('recipient')
                    ->label('Penerima'),

                TextInput::make('recipient_address')
                    ->label('Alamat'),

                Repeater::make('items')
                    ->columnSpanFull()
                    ->table([
                        TableColumn::make('Item'),
                        TableColumn::make('Nominal'),
                        TableColumn::make('Jumlah'),
                    ])
                    ->relationship('items')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Item')
                            ->placeholder('Contoh: VPS, Domain, Hosting, dll')
                            ->required(),
                        TextInput::make('nominal')
                            ->label('Nominal (Rp)')
                            ->numeric()
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Kuantitas')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ]),

                Toggle::make('use_ppn')
                    ->label('Gunakan PPN')
                    ->columnSpanFull()
                    ->default(true),

                TextInput::make('ppn_percentage')
                    ->label('Persentase PPN (%)')
                    ->numeric()
                    ->default(11)
                    ->visible(fn(Get $get) => $get('use_ppn')),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'DRAFT'
                    ])
                    ->default('draft'),

                Textarea::make('note')
                    ->columnSpanFull()

            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('company.name')
                    ->label('Company'),
                TextEntry::make('invoice_date')
                    ->date(),
                TextEntry::make('invoice_number'),
                TextEntry::make('subtotal')
                    ->numeric(),
                IconEntry::make('use_ppn')
                    ->boolean(),
                TextEntry::make('ppn_percentage')
                    ->numeric(),
                TextEntry::make('ppn_amount')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('due_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('recipient')
                    ->placeholder('-'),
                TextEntry::make('recipient_address')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('transaction_number')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label('From')
                    ->searchable(),

                TextColumn::make('recipient')
                    ->label('To')
                    ->searchable(),

                TextColumn::make('invoice_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y'),

                ToggleColumn::make('use_ppn')
                    ->label('PPN'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        'draft' => 'warning'
                    }),

                TextColumn::make('transaction_number')
                    ->label('numb'),

                TextColumn::make('total')
                    ->label('Total')
                    ->summarize(SummarizersSum::make()
                        ->numeric()
                        ->prefix('Rp. ')
                        ->label('Total'))
                    ->numeric()
                    ->prefix('Rp. '),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->default(null) // biar default tampil semua
                    ->placeholder('Semua Status')
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->where('status', $data['value']);
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('printV1')
                        ->label('Cetak v1')
                        ->tooltip('Cetak v1')
                        ->url(fn(Invoice $record) => route('invoice.pdf', $record))
                        ->openUrlInNewTab(),

                    Action::make('printV2')
                        ->label('Cetak v2')
                        ->tooltip('Cetak V2')
                        // ->button()
                        // ->color('success')
                        // ->icon('heroicon-o-printer')
                        ->url(fn(Invoice $record) => route('invoice.pdf2', $record))
                        ->openUrlInNewTab(),
                ])->button()
                    ->label('Cetak')
                    ->icon(icon: 'heroicon-o-printer'),

                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->label('Aksi')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInvoices::route('/'),
        ];
    }
}
