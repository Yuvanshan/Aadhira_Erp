import 'package:flutter/material.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';

class RoomsPage extends StatefulWidget {
  final List<dynamic> rooms;
  final List<dynamic> bookings;
  const RoomsPage({Key? key, required this.rooms, required this.bookings}) : super(key: key);

  @override
  State<RoomsPage> createState() => _RoomsPageState();
}

class _RoomsPageState extends State<RoomsPage> {
  String _selectedStatusFilter = 'All'; // options: 'All', 'Available', 'Booked'

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    if (widget.rooms.isEmpty) {
      return Center(child: Text(l10n.noRooms, style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 16)));
    }

    final filteredRooms = widget.rooms.map((room) {
      bool isBooked = false;
      final now = DateTime.now();

      for (var booking in widget.bookings) {
        final status = booking['status']?.toString().toLowerCase();
        if (status != 'confirmed' && status != 'pending') continue;
        if (booking['check_out'] != null && booking['check_out'].toString().isNotEmpty) continue;

        final lines = booking['hms_booking_lines'] as List<dynamic>? ?? [];
        for (var line in lines) {
          try {
            final arrivalStr = booking['hms_booking_arrival_date_time'];
            final departureStr = booking['hms_booking_departure_date_time'];
            if (arrivalStr == null || departureStr == null) continue;

            final arrival = DateTime.parse(arrivalStr);
            final departure = DateTime.parse(departureStr);

            final roomIdMatch = line['hms_room_id']?.toString() == room['id']?.toString();

            if (roomIdMatch &&
                now.isAfter(arrival.subtract(const Duration(hours: 12))) &&
                now.isBefore(departure.add(const Duration(hours: 12)))) {
              isBooked = true;
              break;
            }
          } catch (_) {}
        }
        if (isBooked) break;
      }

      return {
        'room': room,
        'is_booked': isBooked,
      };
    }).where((item) {
      if (_selectedStatusFilter == 'Available') {
        return !item['is_booked'];
      } else if (_selectedStatusFilter == 'Booked') {
        return item['is_booked'];
      }
      return true;
    }).toList();

    return Column(
      children: [
        // Filter selector chips
        Container(
          padding: const EdgeInsets.symmetric(vertical: 8.0, horizontal: 12.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: ['All', 'Available', 'Booked'].map((status) {
              final isSelected = _selectedStatusFilter == status;
              String label = status;
              if (status == 'All') label = l10n.all;
              if (status == 'Available') label = l10n.available;
              if (status == 'Booked') label = l10n.booked;
              
              return ChoiceChip(
                label: Text(
                  label,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    color: isSelected ? Colors.white : const Color(0xFF94A3B8),
                  ),
                ),
                selected: isSelected,
                selectedColor: const Color(0xFF6366F1),
                backgroundColor: Theme.of(context).colorScheme.surface,
                onSelected: (selected) {
                  if (selected) {
                    setState(() {
                      _selectedStatusFilter = status;
                    });
                  }
                },
              );
            }).toList(),
          ),
        ),
        
        Expanded(
          child: GridView.builder(
            padding: const EdgeInsets.all(16),
            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: 2,
              crossAxisSpacing: 14,
              mainAxisSpacing: 14,
              childAspectRatio: 0.95,
            ),
            itemCount: filteredRooms.length,
            itemBuilder: (context, index) {
              final item = filteredRooms[index];
              final room = item['room'] as Map<String, dynamic>;
              final isBooked = item['is_booked'] as bool;
              
              final type = room['type'] ?? {};
              final roomNo = room['room_number'] ?? "N/A";
              final typeName = type['type'] ?? "Standard";
              final description = type['description'] ?? "No description available.";
              
              final pricings = type['pricings'] ?? [];
              final priceStr = pricings.isNotEmpty ? (pricings[0]['default_price_per_night'] ?? "0.00").toString() : "0.00";
              
              final adults = type['no_of_adult'] ?? 0;
              final children = type['no_of_child'] ?? 0;

               return InkWell(
                onTap: () {
                  showModalBottomSheet(
                    context: context,
                    isScrollControlled: true,
                    backgroundColor: Colors.transparent,
                    builder: (context) {
                      return DraggableScrollableSheet(
                        initialChildSize: 0.55,
                        minChildSize: 0.35,
                        maxChildSize: 0.85,
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
                                crossAxisAlignment: CrossAxisAlignment.start,
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
                                  Text(
                                    "Room $roomNo Details",
                                    style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900),
                                  ),
                                  const SizedBox(height: 16),
                                  Text("Room Type: $typeName", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                                  const SizedBox(height: 8),
                                  Text("${l10n.rate}: Rs. $priceStr / Night", style: const TextStyle(color: Color(0xFF8B5CF6), fontWeight: FontWeight.bold, fontSize: 15)),
                                  const SizedBox(height: 8),
                                  Text("${l10n.capacity}: $adults ${l10n.adults}, $children ${l10n.children}", style: const TextStyle(color: Color(0xFF94A3B8), fontSize: 14)),
                                  const Divider(height: 32, color: Colors.white12),
                                  const Text("Specifications:", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14, color: Colors.white70)),
                                  const SizedBox(height: 8),
                                  Text(description, style: const TextStyle(fontSize: 14, color: Color(0xFF94A3B8), height: 1.4)),
                                  const SizedBox(height: 30),
                                  SizedBox(
                                    width: double.infinity,
                                    child: ElevatedButton(
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
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      );
                    },
                  );
                },
                borderRadius: BorderRadius.circular(20),
                child: Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(20),
                    gradient: LinearGradient(
                      colors: isDark
                          ? [
                              const Color(0xFF131A26).withOpacity(0.95),
                              const Color(0xFF090D16).withOpacity(0.95),
                            ]
                          : [
                              Colors.white,
                              const Color(0xFFF3F4F6),
                            ],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    border: Border.all(
                      color: isBooked ? Colors.redAccent.withOpacity(0.25) : Colors.green.withOpacity(0.25),
                      width: 1.5,
                    ),
                  ),
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "Room $roomNo",
                            style: TextStyle(
                              fontWeight: FontWeight.w900, 
                              fontSize: 17, 
                              color: isDark ? Colors.white : Colors.black87
                            ),
                          ),
                          Container(
                            width: 8,
                            height: 8,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: isBooked ? Colors.redAccent : Colors.green,
                              boxShadow: [
                                BoxShadow(
                                  color: isBooked ? Colors.redAccent.withOpacity(0.5) : Colors.green.withOpacity(0.5),
                                  blurRadius: 4,
                                  spreadRadius: 1,
                                )
                              ]
                            ),
                          )
                        ],
                      ),
                      const SizedBox(height: 8),
                      Text(
                        typeName,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                      ),
                      const Spacer(),
                      Row(
                        children: [
                          const Icon(Icons.person_outline, size: 13, color: Color(0xFF94A3B8)),
                          const SizedBox(width: 2),
                          Text("$adults", style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8))),
                          const SizedBox(width: 8),
                          const Icon(Icons.child_care_outlined, size: 13, color: Color(0xFF94A3B8)),
                          const SizedBox(width: 2),
                          Text("$children", style: const TextStyle(fontSize: 11, color: Color(0xFF94A3B8))),
                        ],
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFF6366F1).withOpacity(0.12),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Text(
                          "Rs. $priceStr/Nt",
                          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Color(0xFF6366F1)),
                        ),
                      )
                    ],
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}
