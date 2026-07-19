import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';

class SalesPage extends StatelessWidget {
  final List<dynamic> sales;
  final List<dynamic> expenses;

  const SalesPage({Key? key, required this.sales, required this.expenses}) : super(key: key);

  String _formatCurrency(dynamic value) {
    if (value == null) return "0.00";
    final parsed = double.tryParse(value.toString());
    if (parsed == null) return value.toString();
    return parsed.toStringAsFixed(2);
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Merge and sort sales and expenses
    final List<Map<String, dynamic>> items = [];
    for (var sale in sales) {
      items.add({
        ...Map<String, dynamic>.from(sale),
        'isExpense': false,
      });
    }
    for (var exp in expenses) {
      items.add({
        ...Map<String, dynamic>.from(exp),
        'isExpense': true,
      });
    }

    items.sort((a, b) {
      final dateA = DateTime.tryParse(a['transaction_date'] ?? '') ?? DateTime(1970);
      final dateB = DateTime.tryParse(b['transaction_date'] ?? '') ?? DateTime(1970);
      return dateB.compareTo(dateA);
    });

    if (items.isEmpty) {
      return Center(child: Text(l10n.noSales, style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 16)));
    }

    return ListView.builder(
      itemCount: items.length,
      itemBuilder: (context, index) {
        final item = items[index];
        final isExpense = item['isExpense'] == true;
        final paymentStatus = item['payment_status'] ?? "Unknown";
        final total = _formatCurrency(item['final_total'] ?? "0.00");

        if (isExpense) {
          final refNo = item['ref_no'] ?? "N/A";
          final category = item['expense_category'] ?? {};
          final categoryName = category['name'] ?? "General Expense";
          return Card(
            margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
              side: BorderSide(color: Colors.white.withOpacity(0.03)),
            ),
            color: Theme.of(context).colorScheme.surface,
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 8),
              leading: CircleAvatar(
                backgroundColor: Colors.redAccent.withOpacity(0.12),
                radius: 22,
                child: const Icon(Icons.money_off, color: Colors.redAccent, size: 20),
              ),
              title: Text(refNo, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 4),
                  Text("Expense  |  $categoryName", style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8))),
                  const SizedBox(height: 2),
                  Text("Status: ${paymentStatus.toUpperCase()}", style: TextStyle(fontSize: 12, color: paymentStatus == 'paid' ? Colors.green : Colors.orangeAccent, fontWeight: FontWeight.bold)),
                ],
              ),
              trailing: Text("-Rs. $total", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.redAccent)),
              onTap: () => _showExpenseDetails(context, item),
            ),
          );
        } else {
          final contact = item['contact'] ?? {};
          final isBooking = item['type'] == 'hms_booking';
          final invoiceNo = (item['invoice_no'] != null && item['invoice_no'].toString().isNotEmpty)
              ? item['invoice_no']
              : (item['ref_no'] ?? "N/A");

          return Card(
            margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
              side: BorderSide(color: Colors.white.withOpacity(0.03)),
            ),
            color: Theme.of(context).colorScheme.surface,
            child: ListTile(
              contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 8),
              leading: CircleAvatar(
                backgroundColor: isBooking 
                    ? const Color(0xFF6366F1).withOpacity(0.12)
                    : Colors.green.withOpacity(0.12),
                radius: 22,
                child: Icon(
                    isBooking ? Icons.hotel : Icons.receipt_long, 
                    color: isBooking ? const Color(0xFF6366F1) : Colors.green, 
                    size: 20),
              ),
              title: Text(invoiceNo, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const SizedBox(height: 4),
                  Text(
                    isBooking 
                        ? "Room Booking  |  ${contact['name'] ?? 'Guest'}"
                        : "${l10n.customer}: ${contact['name'] ?? 'Guest'}", 
                    style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8))),
                  const SizedBox(height: 2),
                  Text("Status: ${paymentStatus.toUpperCase()}", style: TextStyle(fontSize: 12, color: paymentStatus == 'paid' ? Colors.green : Colors.orangeAccent, fontWeight: FontWeight.bold)),
                ],
              ),
              trailing: Text("Rs. $total", style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Colors.green)),
              onTap: () => _showSaleDetails(context, item),
            ),
          );
        }
      },
    );
  }

  void _showExpenseDetails(BuildContext context, Map<String, dynamic> expense) {
    final l10n = AppLocalizations.of(context)!;
    final refNo = expense['ref_no'] ?? "N/A";
    final category = expense['expense_category'] ?? {};
    final categoryName = category['name'] ?? "General Expense";
    final total = _formatCurrency(expense['final_total'] ?? "0.00");
    final status = expense['payment_status'] ?? "N/A";
    final date = expense['transaction_date'] ?? "N/A";
    final notes = expense['additional_notes'] ?? "No additional notes.";

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.5,
          minChildSize: 0.35,
          maxChildSize: 0.8,
          expand: false,
          builder: (context, scrollController) {
            return Container(
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.surface,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: SingleChildScrollView(
                controller: scrollController,
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Center(
                      child: Container(
                        width: 40,
                        height: 4,
                        margin: const EdgeInsets.only(bottom: 20),
                        decoration: BoxDecoration(
                          color: Colors.white24,
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Expense Details", style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.redAccent.withOpacity(0.12),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            refNo,
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.redAccent),
                          ),
                        ),
                      ],
                    ),
                    const Divider(height: 32, color: Colors.white12),
                    Text("Category: $categoryName", style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 8),
                    Text("${l10n.date}: $date", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 8),
                    Text("${l10n.paymentStatus}: ${status.toUpperCase()}", style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: status == 'paid' ? Colors.green : Colors.orangeAccent)),
                    const SizedBox(height: 8),
                    Text("Notes: $notes", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    const Divider(height: 32, color: Colors.white12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Total Amount Spent", style: TextStyle(fontWeight: FontWeight.bold)),
                        Text("-Rs. $total", style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.redAccent)),
                      ],
                    ),
                    const SizedBox(height: 30),
                    ElevatedButton(
                      onPressed: () => Navigator.of(context).pop(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6366F1),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 0,
                      ),
                      child: Text(l10n.close, style: const TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  void _showSaleDetails(BuildContext context, Map<String, dynamic> sale) {
    final l10n = AppLocalizations.of(context)!;
    final contact = sale['contact'] ?? {};
    final isBooking = sale['type'] == 'hms_booking';
    final invoiceNo = (sale['invoice_no'] != null && sale['invoice_no'].toString().isNotEmpty)
        ? sale['invoice_no']
        : (sale['ref_no'] ?? "N/A");
    final total = _formatCurrency(sale['final_total'] ?? "0.00");
    final status = sale['payment_status'] ?? "N/A";
    final tax = _formatCurrency(sale['tax_amount'] ?? "0.00");
    final discount = _formatCurrency(sale['discount_amount'] ?? "0.00");
    final date = sale['transaction_date'] ?? "N/A";
    
    final payments = sale['payment_lines'] as List<dynamic>? ?? [];
    final String method = payments.isNotEmpty ? (payments[0]['method'] ?? "N/A").toString().toUpperCase() : "N/A";
    final isDark = Theme.of(context).brightness == Brightness.dark;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.65,
          minChildSize: 0.45,
          maxChildSize: 0.9,
          expand: false,
          builder: (context, scrollController) {
            return Container(
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.surface,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
              ),
              child: SingleChildScrollView(
                controller: scrollController,
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Center(
                      child: Container(
                        width: 40,
                        height: 4,
                        margin: const EdgeInsets.only(bottom: 20),
                        decoration: BoxDecoration(
                          color: Colors.white24,
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(isBooking ? "Booking Sale Details" : l10n.invoiceDetails, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.green.withOpacity(0.12),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            invoiceNo,
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.green),
                          ),
                        ),
                      ],
                    ),
                    const Divider(height: 32, color: Colors.white12),
                    Text("${l10n.customer}: ${contact['name'] ?? 'Walk-in Customer'}", style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 6),
                    if (contact['mobile'] != null && contact['mobile'].toString().isNotEmpty) ...[
                      Text("Contact No: ${contact['mobile']}", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                      const SizedBox(height: 6),
                    ],
                    if (contact.isNotEmpty) ...[
                      Builder(builder: (context) {
                        final List<String> addressParts = [];
                        final addressLine1 = contact['address_line_1']?.toString() ?? "";
                        final addressLine2 = contact['address_line_2']?.toString() ?? "";
                        final city = contact['city']?.toString() ?? "";
                        final state = contact['state']?.toString() ?? "";
                        final country = contact['country']?.toString() ?? "";
                        final zip = contact['zip_code']?.toString() ?? "";
                        if (addressLine1.isNotEmpty) addressParts.add(addressLine1);
                        if (addressLine2.isNotEmpty) addressParts.add(addressLine2);
                        if (city.isNotEmpty) addressParts.add(city);
                        if (state.isNotEmpty) addressParts.add(state);
                        if (country.isNotEmpty) addressParts.add(country);
                        if (zip.isNotEmpty) addressParts.add(zip);
                        final addressStr = addressParts.isEmpty ? "N/A" : addressParts.join(", ");
                        return Text("Address: $addressStr", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8)));
                      }),
                      const SizedBox(height: 6),
                    ],
                    Text("${l10n.date}: $date", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 6),
                    if (isBooking) ...[
                      Builder(builder: (context) {
                        String arrivalTime = sale['hms_booking_arrival_date_time'] ?? "N/A";
                        String departureTime = sale['hms_booking_departure_date_time'] ?? "N/A";
                        try {
                          if (arrivalTime != "N/A") {
                            arrivalTime = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.parse(arrivalTime));
                          }
                          if (departureTime != "N/A") {
                            departureTime = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.parse(departureTime));
                          }
                        } catch (_) {}
                        return Text("Booking Schedule: $arrivalTime to $departureTime", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8)));
                      }),
                      const SizedBox(height: 6),
                    ],
                    Text("${l10n.paymentMethod}: $method", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 6),
                    Text("${l10n.paymentStatus}: ${status.toUpperCase()}", style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: status == 'paid' ? Colors.green : Colors.orangeAccent)),
                    const Divider(height: 32, color: Colors.white12),
                    if (isBooking) ...[
                      // const Text("Booked Room Schedule:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF94A3B8))),
                      // const SizedBox(height: 8),
                      //  const SizedBox(height: 20),
                    const Text("Booked Room Schedule:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        Expanded(
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF1F5F9),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.white.withOpacity(0.05)),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.login, color: Colors.green, size: 16),
                                    const SizedBox(width: 6),
                                    Text(
                                      l10n.arrival,
                                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 6),
                                Builder(builder: (context) {
                                  String arrivalTime = sale['hms_booking_arrival_date_time'] ?? "N/A";
                                  try {
                                    if (arrivalTime != "N/A") {
                                      arrivalTime = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.parse(arrivalTime));
                                    }
                                  } catch (_) {}
                                  return Text(
                                    arrivalTime,
                                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                                  );
                                }),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: isDark ? const Color(0xFF1E293B) : const Color(0xFFF1F5F9),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.white.withOpacity(0.05)),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    const Icon(Icons.logout, color: Colors.redAccent, size: 16),
                                    const SizedBox(width: 6),
                                    Text(
                                      l10n.departure,
                                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 6),
                                Builder(builder: (context) {
                                  String departureTime = sale['hms_booking_departure_date_time'] ?? "N/A";
                                  try {
                                    if (departureTime != "N/A") {
                                      departureTime = DateFormat('yyyy-MM-dd HH:mm').format(DateTime.parse(departureTime));
                                    }
                                  } catch (_) {}
                                  return Text(
                                    departureTime,
                                    style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                                  );
                                }),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                 
                      Builder(builder: (context) {
                        final bookingLines = sale['hms_booking_lines'] as List<dynamic>? ?? [];
                        if (bookingLines.isEmpty) return const Text("No room lines linked.");
                        return Column(
                          children: bookingLines.map((line) => Container(
                            margin: const EdgeInsets.symmetric(vertical: 4),
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: Theme.of(context).scaffoldBackgroundColor,
                              borderRadius: BorderRadius.circular(10),
                            ),
                            // child: Row(
                            //   children: [
                            //     const Icon(Icons.calendar_month, color: Color(0xFF8B5CF6), size: 16),
                            //     const SizedBox(width: 8),
                            //     Expanded(
                            //       child: Text(
                            //         "Arrival: ${line['arrival_date']} to ${line['departure_date']}",
                            //         style: const TextStyle(fontSize: 12),
                            //       ),
                            //     ),
                            //   ],
                            // ),
                          )).toList(),
                        );
                      }),
                      const Divider(height: 32, color: Colors.white12),
                      Builder(builder: (context) {
                        final bookingExtras = sale['hms_booking_extras'] as List<dynamic>? ?? [];
                        if (bookingExtras.isEmpty) return const SizedBox.shrink();
                        return Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            const Text("Extras & Services:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF94A3B8))),
                            const SizedBox(height: 8),
                            ...bookingExtras.map((extraLine) {
                              final extra = extraLine['extra'] ?? {};
                              final name = extra['name'] ?? "Extra Service";
                              final price = _formatCurrency(extraLine['price'] ?? "0.00");
                              return Padding(
                                padding: const EdgeInsets.symmetric(vertical: 4),
                                child: Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text("- $name", style: const TextStyle(fontSize: 13)),
                                    Text("Rs. $price", style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold)),
                                  ],
                                ),
                              );
                            }).toList(),
                            const Divider(height: 32, color: Colors.white12),
                          ],
                        );
                      }),
                    ],
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(l10n.discount, style: const TextStyle(color: Color(0xFF94A3B8))),
                        Text("-Rs. $discount", style: const TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(l10n.taxAmount, style: const TextStyle(color: Color(0xFF94A3B8))),
                        Text("Rs. $tax", style: const TextStyle(fontWeight: FontWeight.bold)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(l10n.finalTotal, style: const TextStyle(fontWeight: FontWeight.bold)),
                        Text("Rs. $total", style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
                      ],
                    ),
                    const SizedBox(height: 30),
                    ElevatedButton(
                      onPressed: () => Navigator.of(context).pop(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF6366F1),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 0,
                      ),
                      child: Text(l10n.close, style: const TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }
}
