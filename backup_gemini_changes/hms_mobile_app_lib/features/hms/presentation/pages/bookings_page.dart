import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';

class BookingsPage extends StatefulWidget {
  final List<dynamic> bookings;
  const BookingsPage({Key? key, required this.bookings}) : super(key: key);

  @override
  State<BookingsPage> createState() => _BookingsPageState();
}

class _BookingsPageState extends State<BookingsPage> {
  DateTime? _selectedDay;

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    // Generate next 14 days for horizontal timeline
    final List<DateTime> calendarDays = [];
    final DateTime today = DateTime.now();
    for (int i = 0; i < 14; i++) {
      calendarDays.add(DateTime(today.year, today.month, today.day).add(Duration(days: i)));
    }

    final filteredBookings = widget.bookings.where((booking) {
      if (_selectedDay == null) return true;
      
      try {
        final arrivalStr = booking['hms_booking_arrival_date_time'];
        final departureStr = booking['hms_booking_departure_date_time'];
        if (arrivalStr == null || departureStr == null) return false;
        
        final arrival = DateTime.parse(arrivalStr);
        final departure = DateTime.parse(departureStr);
        
        final cleanArrival = DateTime(arrival.year, arrival.month, arrival.day);
        final cleanDeparture = DateTime(departure.year, departure.month, departure.day);
        final cleanSelected = DateTime(_selectedDay!.year, _selectedDay!.month, _selectedDay!.day);

        if ((cleanSelected.isAfter(cleanArrival) || cleanSelected.isAtSameMomentAs(cleanArrival)) &&
            (cleanSelected.isBefore(cleanDeparture) || cleanSelected.isAtSameMomentAs(cleanDeparture))) {
          return true;
        }
      } catch (_) {}
      return false;
    }).toList();

    return Column(
      children: [
        // 📅 HORIZONTAL CALENDAR TIMELINE
        Container(
          height: 84,
          color: Theme.of(context).scaffoldBackgroundColor,
          padding: const EdgeInsets.symmetric(vertical: 10.0),
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: calendarDays.length,
            itemBuilder: (context, index) {
              final date = calendarDays[index];
              final isSelected = _selectedDay != null &&
                  _selectedDay!.year == date.year &&
                  _selectedDay!.month == date.month &&
                  _selectedDay!.day == date.day;
              
              final String weekday = DateFormat('E').format(date);
              final String dayNum = DateFormat('d').format(date);

              return Padding(
                padding: const EdgeInsets.symmetric(horizontal: 6.0),
                child: GestureDetector(
                  onTap: () {
                    setState(() {
                      if (isSelected) {
                        _selectedDay = null; // Toggle off to show all
                      } else {
                        _selectedDay = date;
                      }
                    });
                  },
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    width: 52,
                    decoration: BoxDecoration(
                      color: isSelected ? const Color(0xFF6366F1) : (isDark ? const Color(0xFF131A26) : Colors.white),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: isSelected ? const Color(0xFF6366F1) : Colors.white.withOpacity(0.04),
                      ),
                      boxShadow: isSelected
                          ? [BoxShadow(color: const Color(0xFF6366F1).withOpacity(0.3), blurRadius: 6, offset: const Offset(0, 3))]
                          : null,
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          weekday,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: isSelected ? Colors.white.withOpacity(0.8) : const Color(0xFF94A3B8),
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          dayNum,
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.w900,
                            color: isSelected ? Colors.white : (isDark ? Colors.white : Colors.black87),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ),
        
        // Active calendar filter reset status bar
        if (_selectedDay != null)
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            color: const Color(0xFF13152A),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  "Calendar active: ${DateFormat('yMMMMEEEEd').format(_selectedDay!)}",
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF818CF8)),
                ),
                GestureDetector(
                  onTap: () => setState(() => _selectedDay = null),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.white12,
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      l10n.clearFilter,
                      style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w800, color: Colors.white),
                    ),
                  ),
                ),
              ],
            ),
          ),

        // List View
        Expanded(
          child: filteredBookings.isEmpty
              ? Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24.0),
                    child: Text(
                      l10n.noBookings,
                      style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 15),
                      textAlign: TextAlign.center,
                    ),
                  ),
                )
              : ListView.builder(
                  itemCount: filteredBookings.length,
                  itemBuilder: (context, index) {
                    final booking = filteredBookings[index];
                    final contact = booking['contact'] ?? {};
                    final refNo = booking['ref_no'] ?? "N/A";
                    final status = booking['status'] ?? "Unknown";
                    final total = _formatCurrency(booking['final_total'] ?? "0.00");
                    final arrival = booking['hms_booking_lines'] != null && booking['hms_booking_lines'].isNotEmpty
                        ? booking['hms_booking_lines'][0]['arrival_date'] ?? "N/A"
                        : "N/A";

                    final isCancelled = status.toLowerCase() == 'cancelled';
                    final isPending = status.toLowerCase() == 'pending';
                    final cardBgColor = isCancelled 
                        ? (isDark ? const Color(0xFF451A1A) : const Color(0xFFFEE2E2)) 
                        : (isPending 
                            ? (isDark ? const Color(0xFF382F13) : const Color(0xFFFEF9C3)) 
                            : Theme.of(context).colorScheme.surface);
                    final statusColor = isCancelled 
                        ? Colors.redAccent 
                        : (isPending 
                            ? (isDark ? Colors.amber[400]! : Colors.amber[800]!)
                            : (status.toLowerCase() == 'confirmed' ? Colors.green : Colors.orangeAccent));

                    String formattedArrival = arrival;
                    try {
                      if (booking['hms_booking_arrival_date_time'] != null) {
                        final parsed = DateTime.parse(booking['hms_booking_arrival_date_time']);
                        formattedArrival = DateFormat('yyyy-MM-dd HH:mm').format(parsed);
                      }
                    } catch (_) {}

                    return Card(
                      margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(18),
                        side: BorderSide(color: isCancelled 
                          ? Colors.redAccent.withOpacity(0.2) 
                          : (isPending ? Colors.amber.withOpacity(0.2) : Colors.white.withOpacity(0.03))),
                      ),
                      color: cardBgColor,
                      child: ListTile(
                        contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
                        leading: CircleAvatar(
                          backgroundColor: isCancelled 
                              ? Colors.redAccent.withOpacity(0.12)
                              : (isPending ? Colors.amber.withOpacity(0.12) : const Color(0xFF6366F1).withOpacity(0.12)),
                          radius: 22,
                          child: Icon(Icons.hotel, 
                              color: isCancelled ? Colors.redAccent : (isPending ? Colors.amber : const Color(0xFF6366F1)), 
                              size: 20),
                        ),
                        title: Text(contact['name'] ?? "Walk-in Guest", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 4),
                            Text.rich(
                              TextSpan(
                                text: "Ref: $refNo  |  Status: ",
                                style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8)),
                                children: [
                                  TextSpan(
                                    text: status.toUpperCase(),
                                    style: TextStyle(fontWeight: FontWeight.bold, color: statusColor),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text("${l10n.arrival}: $formattedArrival", style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8))),
                          ],
                        ),
                        trailing: Text("Rs. $total", style: TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: isCancelled ? Colors.redAccent : (isPending ? Colors.amber[700] : const Color(0xFF6366F1)))),
                        onTap: () => _showBookingDetails(context, booking),
                      ),
                    );
                  },
                ),
        ),
      ],
    );
  }

  void _showBookingDetails(BuildContext context, Map<String, dynamic> booking) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final contact = booking['contact'] ?? {};
    final refNo = booking['ref_no'] ?? "N/A";
    final total = _formatCurrency(booking['final_total'] ?? "0.00");
    final status = booking['status'] ?? "N/A";
    final tax = _formatCurrency(booking['tax_amount'] ?? "0.00");
    final bookingLines = booking['hms_booking_lines'] as List<dynamic>? ?? [];

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.6,
          minChildSize: 0.4,
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
                        Text(l10n.bookingDetails, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: const Color(0xFF6366F1).withOpacity(0.12),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            refNo,
                            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF6366F1)),
                          ),
                        ),
                      ],
                    ),
                    const Divider(height: 32, color: Colors.white12),
                    Text("${l10n.customer}: ${contact['name'] ?? 'Guest'}", style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 6),
                    Text("Contact No: ${contact['mobile'] ?? 'N/A'}", style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    const SizedBox(height: 6),
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
                    Text.rich(
                      TextSpan(
                        text: "Status: ",
                        style: const TextStyle(fontSize: 14),
                        children: [
                          TextSpan(
                            text: status.toUpperCase(),
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              color: status.toLowerCase() == 'cancelled'
                                  ? Colors.redAccent
                                  : (status.toLowerCase() == 'pending'
                                      ? Colors.amber[700]
                                      : Colors.green),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
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
                                  String arrivalTime = booking['hms_booking_arrival_date_time'] ?? "N/A";
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
                                  String departureTime = booking['hms_booking_departure_date_time'] ?? "N/A";
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
                    const SizedBox(height: 12),
                    if (bookingLines.isEmpty)
                      const Text("No room lines linked.")
                    else
                      ...bookingLines.map((line) => Container(
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
                    const Divider(height: 32, color: Colors.white12),
                    Builder(builder: (context) {
                      final bookingExtras = booking['hms_booking_extras'] as List<dynamic>? ?? [];
                      if (bookingExtras.isEmpty) return const SizedBox.shrink();
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          const Text("Extras & Services:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13, color: Color(0xFF94A3B8))),
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
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(l10n.taxAmount, style: const TextStyle(color: Color(0xFF94A3B8))),
                        Text("Rs. $tax", style: const TextStyle(fontWeight: FontWeight.bold)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Builder(builder: (context) {
                      final discount = _formatCurrency(booking['discount_amount'] ?? "0.00");
                      final discountType = booking['discount_type'] ?? "fixed";
                      return Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text("Discount", style: TextStyle(color: Color(0xFF94A3B8))),
                          Text(
                            discountType == 'percentage' ? "$discount%" : "-Rs. $discount",
                            style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.redAccent),
                          ),
                        ],
                      );
                    }),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(l10n.finalTotal, style: const TextStyle(fontWeight: FontWeight.bold)),
                        Text("Rs. $total", style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Color(0xFF6366F1))),
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

  String _formatCurrency(dynamic value) {
    if (value == null) return "0.00";
    final parsed = double.tryParse(value.toString());
    if (parsed == null) return value.toString();
    return parsed.toStringAsFixed(2);
  }
}
